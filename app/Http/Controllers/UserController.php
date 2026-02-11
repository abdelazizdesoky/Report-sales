<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    public function index()
    {
        if (auth()->user()->cannot('view users')) {
            abort(403);
        }
        $users = User::with('roles')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        $users = User::all();
        return view('users.create', compact('roles', 'users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'exists:roles,name'],
            'salesman_name' => ['nullable', 'string', 'max:100'],
            'username' => ['nullable', 'string', 'max:50', 'unique:users,username'],
            'supervisor_id' => ['nullable', 'exists:users,id'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'salesman_name' => $request->salesman_name,
            'supervisor_id' => $request->supervisor_id,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('status', 'user-created');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $users = User::where('id', '!=', $user->id)->get(); // Prevent self-supervision
        return view('users.edit', compact('user', 'roles', 'users'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'max:255', 'unique:users,email,'.$user->id],
            'username' => ['nullable', 'string', 'max:50', 'unique:users,username,'.$user->id],
            'role' => ['required', 'exists:roles,name'],
            'salesman_name' => ['nullable', 'string', 'max:100'],
            'supervisor_id' => ['nullable', 'exists:users,id'],
        ]);

        $user->update($request->only('name', 'email', 'username', 'salesman_name', 'supervisor_id'));

        if ($request->filled('password')) {
            $request->validate([
                'password' => ['confirmed', Rules\Password::defaults()],
            ]);
            $user->update(['password' => Hash::make($request->password)]);
        }

        $user->syncRoles($request->role);

        return redirect()->route('users.index')->with('status', 'user-updated');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'لا يمكنك حذف حسابك الحالي.']);
        }

        $user->delete();
        return redirect()->route('users.index')->with('status', 'user-deleted');
    }
}
