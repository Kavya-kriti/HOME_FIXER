<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ProviderProfile;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    // ── Show Forms ────────────────────────────────────────────────────────────

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    // ── Register ──────────────────────────────────────────────────────────────

    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:100'],
            'email'    => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone'    => ['nullable', 'string', 'max:20'],
            'role'     => ['required', 'in:customer,provider'],   // Admin cannot self-register
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'] ?? null,
            'role'     => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        // If registering as a provider, create a blank profile automatically
        if ($user->role === 'provider') {
            ProviderProfile::create([
                'user_id'            => $user->id,
                'years_experience'   => 0,
                'service_radius_km'  => 10,
                'hourly_rate'        => null,
                'avg_rating'         => 0.00,
                'total_jobs'         => 0,
                'is_available'       => false, // Hidden until admin verifies
            ]);
        }

        Log::info('New user registered', ['user_id' => $user->id, 'role' => $user->role]);

        Auth::login($user);

        return redirect()
            ->to($this->dashboardRoute($user->role))
            ->with('success', 'Welcome to HomeFixer, ' . $user->name . '!');
    }

    // ── Login ─────────────────────────────────────────────────────────────────

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate(); // Prevent session fixation

            $user = Auth::user();

            Log::info('User logged in', ['user_id' => $user->id, 'role' => $user->role]);

            return redirect()
                ->intended($this->dashboardRoute($user->role))
                ->with('success', 'Welcome back, ' . $user->name . '!');
        }

        // Generic error — never reveal which field was wrong
        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'These credentials do not match our records.']);
    }

    // ── Logout ────────────────────────────────────────────────────────────────

    public function logout(Request $request): RedirectResponse
    {
        $userName = Auth::user()->name ?? 'User';

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'You have been logged out successfully. See you soon, ' . $userName . '!');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Return the correct dashboard URL for each role.
     * Used after login and register to redirect the user to the right place.
     */
    private function dashboardRoute(string $role): string
    {
        return match($role) {
            'admin'    => route('admin.dashboard'),
            'provider' => route('provider.dashboard'),
            default    => route('customer.dashboard'),
        };
    }
}
