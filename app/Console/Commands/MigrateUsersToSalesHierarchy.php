<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\SalesHierarchyService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateUsersToSalesHierarchy extends Command
{
    protected $signature = 'hierarchy:migrate-users {--apply : Write the changes; without it the command only reports}';

    protected $description = 'Best-effort mapping of existing users onto the SQL Server sales hierarchy';

    public function handle(SalesHierarchyService $hierarchy): int
    {
        $apply = $this->option('apply');

        if (!$apply) {
            $this->warn('Dry run. Re-run with --apply to save.');
        }

        try {
            $chain = $hierarchy->chain();
        } catch (\Exception $e) {
            $this->error('Cannot reach SQL Server: ' . $e->getMessage());
            return self::FAILURE;
        }

        // name -> EMP_CODE, and the set of valid supervisor / manager names
        $salesmanCodes = [];
        $supervisors = [];
        $managers = [];

        foreach ($chain as $node) {
            if (!empty($node->SalesMan) && !empty($node->EMP_CODE)) {
                $salesmanCodes[trim($node->SalesMan)][] = $node->EMP_CODE;
            }
            if (!empty($node->SuperVisor)) {
                $supervisors[trim($node->SuperVisor)] = true;
            }
            if (!empty($node->salesManager)) {
                $managers[trim($node->salesManager)] = true;
            }
        }

        $rows = [];
        $unmapped = [];
        $changes = 0;

        foreach (User::with('roles')->get() as $user) {
            if ($hierarchy->isUnrestricted($user)) {
                $rows[] = [$user->name, $user->roles->pluck('name')->implode(', '), 'unrestricted', '-', 'sees everything'];
                continue;
            }

            [$level, $value, $note] = $this->resolve($user, $salesmanCodes, $supervisors, $managers);

            if ($level === null) {
                $unmapped[] = $user;
                $rows[] = [$user->name, $user->roles->pluck('name')->implode(', '), '-', '-', $note];
                continue;
            }

            $rows[] = [$user->name, $user->roles->pluck('name')->implode(', '), $level, $value, $note];
            $changes++;

            if ($apply) {
                $user->update(['hierarchy_level' => $level, 'hierarchy_value' => $value]);
            }
        }

        $this->table(['User', 'Role', 'Level', 'Value', 'Note'], $rows);

        if ($apply) {
            SalesHierarchyService::flush();
            $this->info("Mapped {$changes} user(s).");
        } else {
            $this->info("Would map {$changes} user(s).");
        }

        if (!empty($unmapped)) {
            $this->warn(count($unmapped) . ' user(s) could not be mapped and will see NO data until bound manually:');
            foreach ($unmapped as $user) {
                $this->line('  - ' . $user->name . ' (' . $user->email . ')');
            }
        }

        return self::SUCCESS;
    }

    /**
     * Try, in order: the user's own salesman_name, then any salesman name
     * assigned to them in the legacy manager_salesman table, then their
     * display name against supervisor / manager names.
     */
    private function resolve(User $user, array $salesmanCodes, array $supervisors, array $managers): array
    {
        $name = trim((string) $user->name);

        if (isset($managers[$name])) {
            return [SalesHierarchyService::LEVEL_SALES_MANAGER, $name, 'matched by name against salesManager'];
        }

        if (isset($supervisors[$name])) {
            return [SalesHierarchyService::LEVEL_SUPERVISOR, $name, 'matched by name against SuperVisor'];
        }

        $ownName = trim((string) $user->salesman_name);
        if ($ownName !== '' && isset($salesmanCodes[$ownName])) {
            $codes = array_unique($salesmanCodes[$ownName]);
            if (count($codes) === 1) {
                return [SalesHierarchyService::LEVEL_SALESMAN, reset($codes), 'matched via salesman_name'];
            }
            return [null, null, "salesman_name '{$ownName}' maps to " . count($codes) . ' employee codes, pick one manually'];
        }

        // Legacy manager_salesman rows: only usable when they all resolve to
        // the same supervisor or manager, otherwise a human must decide.
        $legacy = DB::table('manager_salesman')->where('manager_id', $user->id)->pluck('salesman_name')->all();

        if (!empty($legacy)) {
            return [null, null, count($legacy) . ' legacy salesman link(s), needs a manual level'];
        }

        if ($ownName !== '') {
            return [null, null, "salesman_name '{$ownName}' not found in the hierarchy"];
        }

        return [null, null, 'nothing to match on'];
    }
}
