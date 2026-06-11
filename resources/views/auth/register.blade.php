@extends('layouts.auth')

@section('title', 'Create Account')

@section('content')
<div class="auth-wrap">

    {{-- ── Left decorative panel ──────────────────────────────────── --}}
    <aside class="auth-panel">

        <a href="{{ route('login') }}" class="panel-logo">
            <span class="panel-logo-icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M2.25 12l8.954-8.955a1.126 1.126 0 011.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                </svg>
            </span>
            <span class="panel-logo-name">HomeFixer</span>
        </a>

        <div class="panel-content">
            <h2 class="panel-tagline">
                Join the<br>
                <em>smarter</em> way<br>
                to fix homes.
            </h2>
            <p class="panel-body">
                Whether you need a plumber at 9am or want to grow your repair business,
                HomeFixer's AI connects the right people in the right place at the right time.
            </p>

            <div class="panel-features">
                <div class="panel-feature">
                    <span class="panel-feature-dot"></span>
                    Free to join — no subscription fees
                </div>
                <div class="panel-feature">
                    <span class="panel-feature-dot"></span>
                    Providers get verified &amp; listed instantly
                </div>
                <div class="panel-feature">
                    <span class="panel-feature-dot"></span>
                    AI recommends your services to nearby customers
                </div>
                <div class="panel-feature">
                    <span class="panel-feature-dot"></span>
                    Secure payments &amp; transparent reviews
                </div>
            </div>
        </div>

        <p style="position:relative; font-size:.75rem; color:#78716C;">
            &copy; {{ date('Y') }} HomeFixer &mdash; MCA Final Year Project
        </p>
    </aside>

    {{-- ── Right: register form ───────────────────────────────────── --}}
    <main class="auth-form-side">
        <div class="auth-card">

            <h1 class="auth-heading">Create account</h1>
            <p class="auth-sub">
                Already have one?
                <a href="{{ route('login') }}">Sign in here</a>
            </p>

            {{-- Flash errors --}}
            @if($errors->any())
                <div class="flash flash-error">
                    <i data-feather="alert-circle"></i>
                    <span>Please fix the errors below and try again.</span>
                </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" novalidate>
                @csrf

                {{-- ── Role Picker ────────────────────────────────── --}}
                <div style="margin-bottom:1.1rem;">
                    <label style="margin-bottom:.6rem; display:block;">I am joining as</label>

                    <div class="role-picker">
                        {{-- Customer --}}
                        <input
                            type="radio" name="role" id="role_customer"
                            value="customer" class="role-option"
                            {{ old('role', 'customer') === 'customer' ? 'checked' : '' }}
                        >
                        <label for="role_customer" class="role-label">
                            <span class="role-label-icon">
                                <i data-feather="user"></i>
                            </span>
                            Customer
                            <span style="font-size:.75rem; font-weight:400; color:inherit; opacity:.7;">
                                I need services
                            </span>
                        </label>

                        {{-- Provider --}}
                        <input
                            type="radio" name="role" id="role_provider"
                            value="provider" class="role-option"
                            {{ old('role') === 'provider' ? 'checked' : '' }}
                        >
                        <label for="role_provider" class="role-label">
                            <span class="role-label-icon">
                                <i data-feather="tool"></i>
                            </span>
                            Provider
                            <span style="font-size:.75rem; font-weight:400; color:inherit; opacity:.7;">
                                I offer services
                            </span>
                        </label>
                    </div>

                    @error('role')
                        <p class="field-error">
                            <i data-feather="alert-circle" style="width:13px;height:13px;"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- ── Full Name ───────────────────────────────────── --}}
                <div class="form-group">
                    <label for="name">Full name</label>
                    <div class="input-wrap">
                        <i data-feather="user" class="input-icon"></i>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            placeholder="Ravi Kumar"
                            autocomplete="name"
                            class="{{ $errors->has('name') ? 'is-error' : '' }}"
                            required
                        >
                    </div>
                    @error('name')
                        <p class="field-error">
                            <i data-feather="alert-circle" style="width:13px;height:13px;"></i>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- ── Email + Phone ────────────────────────────────── --}}
                <div class="form-row">
                    <div class="form-group" style="margin-bottom:0;">
                        <label for="email">Email</label>
                        <div class="input-wrap">
                            <i data-feather="mail" class="input-icon"></i>
                            <input
                                type="email"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                placeholder="you@example.com"
                                autocomplete="email"
                                class="{{ $errors->has('email') ? 'is-error' : '' }}"
                                required
                            >
                        </div>
                        @error('email')
                            <p class="field-error">
                                <i data-feather="alert-circle" style="width:13px;height:13px;"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-bottom:0;">
                        <label for="phone">Phone <span style="font-weight:400;opacity:.6;">(optional)</span></label>
                        <div class="input-wrap">
                            <i data-feather="phone" class="input-icon"></i>
                            <input
                                type="tel"
                                id="phone"
                                name="phone"
                                value="{{ old('phone') }}"
                                placeholder="9876543210"
                                autocomplete="tel"
                                class="{{ $errors->has('phone') ? 'is-error' : '' }}"
                            >
                        </div>
                        @error('phone')
                            <p class="field-error">
                                <i data-feather="alert-circle" style="width:13px;height:13px;"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div style="margin-bottom:1.1rem;"></div>

                {{-- ── Password + Confirm ──────────────────────────── --}}
                <div class="form-row">
                    <div class="form-group" style="margin-bottom:0;">
                        <label for="password">Password</label>
                        <div class="input-wrap">
                            <i data-feather="lock" class="input-icon"></i>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Min 8 characters"
                                autocomplete="new-password"
                                class="{{ $errors->has('password') ? 'is-error' : '' }}"
                                required
                            >
                        </div>
                        @error('password')
                            <p class="field-error">
                                <i data-feather="alert-circle" style="width:13px;height:13px;"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="form-group" style="margin-bottom:0;">
                        <label for="password_confirmation">Confirm</label>
                        <div class="input-wrap">
                            <i data-feather="shield" class="input-icon"></i>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                placeholder="Repeat password"
                                autocomplete="new-password"
                                required
                            >
                        </div>
                    </div>
                </div>

                <div style="margin-bottom:1.4rem;"></div>

                {{-- ── Terms note ──────────────────────────────────── --}}
                <p style="font-size:.78rem; color:var(--ink-muted); margin-bottom:1.1rem; line-height:1.5;">
                    By creating an account you agree to our
                    <a href="#" style="color:var(--amber-dark); text-decoration:none;">Terms of Service</a>
                    and
                    <a href="#" style="color:var(--amber-dark); text-decoration:none;">Privacy Policy</a>.
                </p>

                <button type="submit" class="btn-primary">
                    <i data-feather="user-plus"></i>
                    Create my HomeFixer account
                </button>
            </form>

        </div>
    </main>
</div>
@endsection
