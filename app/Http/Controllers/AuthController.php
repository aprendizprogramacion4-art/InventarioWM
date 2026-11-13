<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (session('admin_authenticated')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string'],
        ]);

        $role = null;
        $username = $credentials['username'];
        $adminUsername = (string) config('inventory.admin_username');
        $adminHash = (string) config('inventory.admin_password_hash');
        $employeeUsername = (string) config('inventory.employee_username');
        $employeeHash = (string) config('inventory.employee_password_hash');

        if ($username === $adminUsername && $adminHash !== '' && Hash::check($credentials['password'], $adminHash)) {
            $role = 'admin';
        } elseif ($username === $employeeUsername && $employeeHash !== '' && Hash::check($credentials['password'], $employeeHash)) {
            $role = 'employee';
        }

        if ($role === null) {
            return back()->withErrors(['username' => 'Usuario o contraseña incorrectos.'])->onlyInput('username');
        }

        $request->session()->regenerate();
        $request->session()->put([
            'admin_authenticated' => true,
            'admin_username' => $username,
            'role' => $role,
        ]);

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
