<?php

namespace App\Http\Controllers\Private;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all(); // List all users
        return view('private.users.index', compact('users'));
    }

    public function create()
    {
        return view('private.users.create'); // Form to create a new user
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required|in:sales,purchasing,warehouse,route',
        ]);

        // Buscar o crear el rol correspondiente
        $role = Role::firstOrCreate(['name' => $request->role]);

        // Preparar los datos del usuario
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role_id' => $role->id
        ];
        
        $user = User::create($userData);

        return redirect()->route('private.users.index');
    }

    public function edit(User $user)
    {
        return view('private.users.edit', compact('user')); // Form to edit user details
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password ? bcrypt($request->password) : $user->password,
            'role_id' => $request->role_id,
        ]);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado con éxito.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('private.users.index');
    }

    // New profile method to show user details
    public function profile(User $user)
    {
        return view('private.users.profile', compact('user')); // Display user profile
    }
}