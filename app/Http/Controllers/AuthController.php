<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOGIN_DECAY_SECONDS = 60;

    public function create(): View|RedirectResponse
    {
        if (session('inventory_authenticated') || session('admin_authenticated')) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function store(Request $request, RateLimiter $limiter): RedirectResponse
    {
        $credentials = $request->validate([
            'username' => ['required', 'string', 'max:100'],
            'password' => ['required', 'string', 'max:255'],
        ]);

        $throttleKey = $this->throttleKey($credentials['username'], $request);

        if ($limiter->tooManyAttempts($throttleKey, self::MAX_LOGIN_ATTEMPTS)) {
            $seconds = $limiter->availableIn($throttleKey);

            return back()
                ->withErrors(['username' => "Demasiados intentos. Vuelve a intentarlo en {$seconds} segundos."])
                ->onlyInput('username');
        }

        $configuredAdmin = $credentials['username'] === (string) config('inventory.admin_username')
            && (string) config('inventory.admin_password_hash') !== ''
            && Hash::check($credentials['password'], (string) config('inventory.admin_password_hash'));
        $user = User::query()->where('username', $credentials['username'])->where('is_active', true)->first();
        $validEmployee = $user !== null && $user->role === 'employee' && Hash::check($credentials['password'], $user->password);

        if (!$configuredAdmin && !$validEmployee) {
            $limiter->hit($throttleKey, self::LOGIN_DECAY_SECONDS);

            return back()
                ->withErrors(['username' => 'Usuario o contraseña incorrectos.'])
                ->onlyInput('username');
        }

        $limiter->clear($throttleKey);
        $request->session()->regenerate();
        $request->session()->put([
            'inventory_authenticated' => true,
            'inventory_role' => $configuredAdmin ? 'admin' : 'employee',
            'inventory_username' => $configuredAdmin ? (string) config('inventory.admin_username') : $user->username,
            'inventory_user_id' => $configuredAdmin ? null : $user->id,
        ]);

        if ($configuredAdmin) {
            $request->session()->put('admin_authenticated', true);
        } else {
            $request->session()->forget('admin_authenticated');
        }

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function throttleKey(string $username, Request $request): string
    {
        return Str::transliterate(Str::lower($username).'|'.$request->ip());
    }
}
