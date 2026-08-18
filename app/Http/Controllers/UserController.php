<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use App\Services\ReportService;
use App\Services\SalesHierarchyService;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function __construct(
        protected SalesHierarchyService $hierarchy
    ) {}

    public function index(Request $request)
    {
        if (auth()->user()->cannot('view users')) {
            abort(403);
        }

        $query = User::with('roles');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%")
                  ->orWhere('salesman_name', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(10)->withQueryString();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        if (auth()->user()->cannot('create users')) {
            abort(403);
        }

        $roles = $this->assignableRoles();
        $users = User::where('is_enabled', true)->get();
        $hierarchyOptions = $this->hierarchy->allOptions();
        $hierarchyLabels = SalesHierarchyService::LEVEL_LABELS;

        return view('users.create', compact('roles', 'users', 'hierarchyOptions', 'hierarchyLabels'));
    }

    public function store(Request $request)
    {
        if (auth()->user()->cannot('create users')) {
            abort(403);
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,name'],
            'salesman_name' => ['nullable', 'string', 'max:100'],
            'region' => ['nullable', 'string', 'max:100'],
            'hierarchy_level' => ['nullable', Rule::in(array_keys(SalesHierarchyService::LEVEL_COLUMNS))],
            'hierarchy_value' => ['nullable', 'string', 'max:150', 'required_with:hierarchy_level'],
            'username' => ['nullable', 'string', 'max:50', 'unique:users,username'],
            'supervisor_id' => ['nullable', 'exists:users,id'],
            'is_enabled' => ['boolean'],
        ]);

        $this->authorizeRoleAssignment($request->role);
        $this->validateHierarchyBinding($request->hierarchy_level, $request->hierarchy_value);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'salesman_name' => $request->salesman_name,
            'region' => $request->region,
            'hierarchy_level' => $request->hierarchy_level ?: null,
            'hierarchy_value' => $request->hierarchy_level ? $request->hierarchy_value : null,
            'supervisor_id' => $request->supervisor_id,
            'is_enabled' => $request->boolean('is_enabled', true),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('status', 'user-created');
    }

    public function edit(User $user)
    {
        if (auth()->user()->cannot('edit users')) {
            abort(403);
        }

        $this->authorizeTargetUser($user);

        $roles = $this->assignableRoles();
        $users = User::where('id', '!=', $user->id)->get(); // Prevent self-supervision
        $hierarchyOptions = $this->hierarchy->allOptions();
        $hierarchyLabels = SalesHierarchyService::LEVEL_LABELS;

        return view('users.edit', compact('user', 'roles', 'users', 'hierarchyOptions', 'hierarchyLabels'));
    }

    public function update(Request $request, User $user)
    {
        if (auth()->user()->cannot('edit users')) {
            abort(403);
        }

        $this->authorizeTargetUser($user);

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'max:255', 'unique:users,email,'.$user->id],
            'username' => ['nullable', 'string', 'max:50', 'unique:users,username,'.$user->id],
            'role' => ['required', 'exists:roles,name'],
            'salesman_name' => ['nullable', 'string', 'max:100'],
            'region' => ['nullable', 'string', 'max:100'],
            'hierarchy_level' => ['nullable', Rule::in(array_keys(SalesHierarchyService::LEVEL_COLUMNS))],
            'hierarchy_value' => ['nullable', 'string', 'max:150', 'required_with:hierarchy_level'],
            'supervisor_id' => ['nullable', 'exists:users,id'],
            'is_enabled' => ['required', 'boolean'],
        ]);

        $this->authorizeRoleAssignment($request->role);
        $this->validateHierarchyBinding($request->hierarchy_level, $request->hierarchy_value);

        // Prevent users from escalating or locking out their own account
        if ($user->id === auth()->id()) {
            if (!$user->hasRole($request->role)) {
                return back()->withErrors(['role' => 'لا يمكنك تغيير رتبة حسابك الحالي.']);
            }
            if (!$request->boolean('is_enabled')) {
                return back()->withErrors(['is_enabled' => 'لا يمكنك تعطيل حسابك الحالي.']);
            }
        }

        $user->update($request->only('name', 'email', 'username', 'salesman_name', 'region', 'supervisor_id', 'is_enabled') + [
            'hierarchy_level' => $request->hierarchy_level ?: null,
            'hierarchy_value' => $request->hierarchy_level ? $request->hierarchy_value : null,
        ]);

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->syncRoles($request->role);

        // Role / supervisor / status changes alter who may see which rows
        ReportService::flushFilterOptions();

        return redirect()->route('users.index')->with('status', 'user-updated');
    }

    public function toggleStatus(User $user)
    {
        if (auth()->user()->cannot('edit users')) {
            abort(403);
        }

        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'لا يمكنك تعطيل حسابك الحالي.']);
        }

        $this->authorizeTargetUser($user);

        $user->update(['is_enabled' => !$user->is_enabled]);

        ReportService::flushFilterOptions();

        return redirect()->route('users.index')->with('status', 'user-status-updated');
    }

    public function destroy(User $user)
    {
        if (auth()->user()->cannot('delete users')) {
            abort(403);
        }

        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'لا يمكنك حذف حسابك الحالي.']);
        }

        $this->authorizeTargetUser($user);

        $user->delete();
        return redirect()->route('users.index')->with('status', 'user-deleted');
    }

    /**
     * Roles the current user is allowed to hand out.
     * Only an Admin may assign the Admin role.
     */
    private function assignableRoles()
    {
        $roles = Role::all();

        if (!auth()->user()->hasRole('Admin')) {
            $roles = $roles->where('name', '!=', 'Admin')->values();
        }

        return $roles;
    }

    /**
     * Block privilege escalation through the role field.
     */
    private function authorizeRoleAssignment(string $role): void
    {
        if ($role === 'Admin' && !auth()->user()->hasRole('Admin')) {
            abort(403, 'غير مصرح لك بمنح رتبة الأدمن.');
        }
    }

    /**
     * The value must be a node that actually exists in the SQL Server
     * hierarchy, so a typo cannot silently leave a user seeing nothing.
     * Skipped when SQL Server is unreachable, to keep the form usable.
     */
    private function validateHierarchyBinding(?string $level, ?string $value): void
    {
        if (!SalesHierarchyService::isValidLevel($level) || $value === null || $value === '') {
            return;
        }

        $options = $this->hierarchy->allOptions()[$level] ?? [];

        if (empty($options)) {
            return;
        }

        if (!array_key_exists($value, $options)) {
            abort(422, 'القيمة المختارة غير موجودة في هيكل المبيعات.');
        }
    }

    /**
     * Non-admins may not modify or delete admin accounts.
     */
    private function authorizeTargetUser(User $user): void
    {
        if ($user->hasRole('Admin') && !auth()->user()->hasRole('Admin')) {
            abort(403, 'غير مصرح لك بتعديل حسابات الأدمن.');
        }
    }
}
