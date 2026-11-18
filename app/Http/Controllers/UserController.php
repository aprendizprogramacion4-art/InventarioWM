<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(): View
    {
        return view('users.index', [
            'users' => User::query()->where('role', 'employee')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'username' => ['required', 'string', 'alpha_dash', 'max:100', 'unique:users,username', Rule::notIn([(string) config('inventory.admin_username')])],
            'password' => ['required', 'string', 'min:8', 'max:255', 'confirmed'],
        ]);

        User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'password' => Hash::make($data['password']),
            'role' => 'employee',
            'is_active' => true,
        ]);

        return redirect()->route('users.index')->with('status', 'Usuario creado correctamente.');
    }

    public function destroy(User $user): RedirectResponse
    {
        abort_unless($user->role === 'employee', 404);

        if (session('inventory_user_id') === $user->id) {
            return back()->withErrors(['user' => 'No puedes eliminar tu propia cuenta mientras está activa.']);
        }

        $user->delete();

        return redirect()->route('users.index')->with('status', 'Usuario eliminado correctamente.');
    }
}
