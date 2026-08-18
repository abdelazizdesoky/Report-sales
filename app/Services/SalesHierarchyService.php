<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Reads the sales hierarchy that already lives in SQL Server.
 *
 * BI_ACTIVE_CUSTOMERS holds one row per customer carrying the full chain:
 *   CUST_CODE -> EMP_CODE / SalesMan -> SuperVisor -> salesManager
 *
 * A user is bound to one node of that chain (level + value) and sees every
 * customer sitting underneath it. Nothing about the hierarchy is duplicated
 * in this application: a salesman added in SQL Server shows up immediately
 * under the right supervisor.
 */
class SalesHierarchyService
{
    public const SOURCE_TABLE = 'BI_ACTIVE_CUSTOMERS';

    public const LEVEL_SALESMAN = 'salesman';
    public const LEVEL_SUPERVISOR = 'supervisor';
    public const LEVEL_SALES_MANAGER = 'sales_manager';

    /**
     * Each level and the column that identifies it in the source view.
     * Salesmen are matched on their employee code because names repeat and
     * get re-spelled; the other two levels only exist as names.
     */
    public const LEVEL_COLUMNS = [
        self::LEVEL_SALESMAN => 'EMP_CODE',
        self::LEVEL_SUPERVISOR => 'SuperVisor',
        self::LEVEL_SALES_MANAGER => 'salesManager',
    ];

    public const LEVEL_LABELS = [
        self::LEVEL_SALESMAN => 'مندوب',
        self::LEVEL_SUPERVISOR => 'مشرف',
        self::LEVEL_SALES_MANAGER => 'مدير مبيعات',
    ];

    /**
     * Roles that bypass hierarchy filtering entirely.
     */
    public const UNRESTRICTED_ROLES = ['Admin', 'General Manager', 'Coordinator'];

    private const CACHE_TTL = 3600;
    private const CACHE_VERSION_KEY = 'sales_hierarchy_version';

    public static function isValidLevel(?string $level): bool
    {
        return $level !== null && array_key_exists($level, self::LEVEL_COLUMNS);
    }

    public static function columnFor(string $level): string
    {
        if (!self::isValidLevel($level)) {
            throw new \InvalidArgumentException("Unknown hierarchy level: {$level}");
        }

        return self::LEVEL_COLUMNS[$level];
    }

    /**
     * Drop every cached hierarchy list. Call after the source view changes.
     */
    public static function flush(): void
    {
        Cache::forever(self::CACHE_VERSION_KEY, self::version() + 1);
    }

    private static function version(): int
    {
        return (int) Cache::get(self::CACHE_VERSION_KEY, 1);
    }

    private function cacheKey(string $suffix): string
    {
        return 'sales_hierarchy_v' . self::version() . '_' . $suffix;
    }

    private function source()
    {
        return DB::connection('sqlsrv')->table(self::SOURCE_TABLE);
    }

    /**
     * Whether this user sees everything, regardless of hierarchy.
     */
    public function isUnrestricted(?User $user): bool
    {
        return $user !== null && $user->hasAnyRole(self::UNRESTRICTED_ROLES);
    }

    /**
     * Apply the hierarchy restriction to a query over a report table that has
     * a customer code column.
     *
     * Runs as a correlated subquery on the same connection, so the customer
     * codes never travel through PHP.
     */
    public function applyScope($query, ?User $user, string $customerCodeColumn = 'كود_العميل')
    {
        if ($this->isUnrestricted($user)) {
            return $query;
        }

        $level = $user?->hierarchy_level;
        $value = $user?->hierarchy_value;

        // No hierarchy binding means no data (fail closed).
        if ($user === null || !self::isValidLevel($level) || $value === null || $value === '') {
            return $query->whereRaw('1 = 0');
        }

        $column = self::columnFor($level);

        return $query->whereIn($customerCodeColumn, function ($sub) use ($column, $value) {
            $sub->select('CUST_CODE')
                ->from(self::SOURCE_TABLE)
                ->where($column, $value);
        });
    }

    /**
     * Salesmen as [EMP_CODE => "name (code)"], for the user form.
     */
    public function salesmanOptions(): array
    {
        return Cache::remember($this->cacheKey('salesmen'), self::CACHE_TTL, function () {
            $rows = $this->source()
                ->select('EMP_CODE', 'SalesMan')
                ->whereNotNull('EMP_CODE')
                ->whereNotNull('SalesMan')
                ->distinct()
                ->orderBy('SalesMan')
                ->get();

            $options = [];
            foreach ($rows as $row) {
                $options[$row->EMP_CODE] = $row->SalesMan . ' (' . $row->EMP_CODE . ')';
            }

            return $options;
        });
    }

    /**
     * Supervisor names as [name => name].
     */
    public function supervisorOptions(): array
    {
        return $this->nameOptions('supervisors', 'SuperVisor');
    }

    /**
     * Sales manager names as [name => name].
     */
    public function salesManagerOptions(): array
    {
        return $this->nameOptions('managers', 'salesManager');
    }

    private function nameOptions(string $cacheSuffix, string $column): array
    {
        return Cache::remember($this->cacheKey($cacheSuffix), self::CACHE_TTL, function () use ($column) {
            $names = $this->source()
                ->whereNotNull($column)
                ->where($column, '!=', '')
                ->distinct()
                ->orderBy($column)
                ->pluck($column)
                ->all();

            return array_combine($names, $names) ?: [];
        });
    }

    /**
     * Every option keyed by level, for the dependent dropdowns in the user form.
     * Returns empty lists rather than failing when SQL Server is unreachable.
     */
    public function allOptions(): array
    {
        try {
            return [
                self::LEVEL_SALESMAN => $this->salesmanOptions(),
                self::LEVEL_SUPERVISOR => $this->supervisorOptions(),
                self::LEVEL_SALES_MANAGER => $this->salesManagerOptions(),
            ];
        } catch (\Exception $e) {
            return [
                self::LEVEL_SALESMAN => [],
                self::LEVEL_SUPERVISOR => [],
                self::LEVEL_SALES_MANAGER => [],
            ];
        }
    }

    /**
     * The raw hierarchy chain, one row per salesman, for the org chart.
     */
    public function chain()
    {
        return Cache::remember($this->cacheKey('chain'), self::CACHE_TTL, function () {
            return $this->source()
                ->select('EMP_CODE', 'SalesMan', 'SuperVisor', 'salesManager')
                ->selectRaw('COUNT(*) as customers_count')
                ->whereNotNull('EMP_CODE')
                ->groupBy('EMP_CODE', 'SalesMan', 'SuperVisor', 'salesManager')
                ->orderBy('salesManager')
                ->orderBy('SuperVisor')
                ->orderBy('SalesMan')
                ->get();
        });
    }

    /**
     * Human readable description of a user's binding, for listings.
     */
    public function describe(?User $user): string
    {
        if ($this->isUnrestricted($user)) {
            return 'كل البيانات';
        }

        if ($user === null || !self::isValidLevel($user->hierarchy_level) || empty($user->hierarchy_value)) {
            return 'غير مربوط';
        }

        $label = self::LEVEL_LABELS[$user->hierarchy_level];

        if ($user->hierarchy_level === self::LEVEL_SALESMAN) {
            $name = $this->salesmanOptions()[$user->hierarchy_value] ?? $user->hierarchy_value;
            return $label . ': ' . $name;
        }

        return $label . ': ' . $user->hierarchy_value;
    }
}
