@extends('layouts.auth')

@section('title', 'Sign In')

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
                Your home,<br>
                <em>perfectly</em><br>
                maintained.
            </h2>
            <p class="panel-body">
                Our AI matches your maintenance issue to the right expert in seconds —
                no guesswork, no wasted calls, no overcharging.
            </p>

            <div class="panel-features">
                <div class="panel-feature">
                    <span class="panel-feature-dot"></span>
                    AI-powered service recommendations
                </div>
                <div class="panel-feature">
                    <span class="panel-feature-dot"></span>
                    Verified local providers in Ranchi &amp; nearby
                </div>
                <div class="panel-feature">
                    <span class="panel-feature-dot"></span>
                    Transparent pricing &amp; reviews
                </div>
                <div class="panel-feature">
                    <span class="panel-feature-dot"></span>
                    Real-time job tracking
                </div>
            </div>
        </div>

        <p style="position:relative; font-size:.75rem; color:#78716C;">
            &copy; {{ date('Y') }} HomeFixer &mdash; MCA Final Year Project
        </p>
    </aside>

    {{-- ── Right: login form ──────────────────────────────────────── --}}
    <main class="auth-form-side">
        <div class="auth-card">

            <h1 class="auth-heading">Welcome back</h1>
            <p class="auth-sub">
                Don't have an account?
                <a href="{{ route('register') }}">Create one free</a>
            </p>

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="flash flash-success">
                    <i data-feather="check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="flash flash-error">
                    <i data-feather="alert-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Login Form --}}
            <form method="POST" action="{{ route('login.post') }}" novalidate>
                @csrf

                {{-- Email --}}
                <div class="form-group">
                    <label for="email">Email address</label>
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

                {{-- Password --}}
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <i data-feather="lock" class="input-icon"></i>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="••••••••"
                            autocomplete="current-password"
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

                {{-- Remember + Forgot --}}
                <div class="form-meta">
                    <label class="check-label" style="cursor:pointer;">
                        <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                        Remember me
                    </label>
                    <a href="#" class="forgot-link">Forgot password?</a>
                </div>

                <button type="submit" class="btn-primary">
                    <i data-feather="log-in"></i>
                    Sign in to HomeFixer
                </button>
            </form>

            <div class="divider">or continue as</div>

            <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:.6rem; font-size:.8rem; text-align:center;">
                <a href="#" onclick="fillDemo('ravi@example.com')"
                   style="padding:.6rem .5rem; border:1.5px solid var(--border); border-radius:var(--radius-sm);
                          color:var(--ink-light); text-decoration:none; transition:border-color .15s;"
                   onmouseover="this.style.borderColor='var(--amber)'"
                   onmouseout="this.style.borderColor='var(--border)'">
                    Customer
                </a>
                <a href="#" onclick="fillDemo('ramesh@provider.com')"
                   style="padding:.6rem .5rem; border:1.5px solid var(--border); border-radius:var(--radius-sm);
                          color:var(--ink-light); text-decoration:none; transition:border-color .15s;"
                   onmouseover="this.style.borderColor='var(--amber)'"
                   onmouseout="this.style.borderColor='var(--border)'">
                    Provider
                </a>
                <a href="#" onclick="fillDemo('finalprojectmini@gmail.com')"
                   style="padding:.6rem .5rem; border:1.5px solid var(--border); border-radius:var(--radius-sm);
                          color:var(--ink-light); text-decoration:none; transition:border-color .15s;"
                   onmouseover="this.style.borderColor='var(--amber)'"
                   onmouseout="this.style.borderColor='var(--border)'">
                    Admin
                </a>
            </div>
            <p style="text-align:center; font-size:.75rem; color:var(--ink-muted); margin-top:.5rem;">
                Demo accounts (password: <code style="background:var(--cream);padding:.1rem .3rem;border-radius:4px;">password</code>)
            </p>

        </div>
    </main>
</div>
@endsection

@push('scripts')
<script>
function fillDemo(email) {
    event.preventDefault();
    document.getElementById('email').value = email;
    document.getElementById('password').value = 'password';
    feather.replace({ 'stroke-width': 1.75 });
}
</script>
@endpush
