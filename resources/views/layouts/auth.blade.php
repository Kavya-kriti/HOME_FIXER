<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'HomeFixer') — AI Home Maintenance</title>

    {{-- Google Fonts: DM Serif Display + DM Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display:ital@0;1&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,300&display=swap" rel="stylesheet">

    {{-- Feather Icons --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.2/feather.min.js" defer></script>

    <style>
        /* ── Reset & Base ──────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --cream:       #FAF8F4;
            --ink:         #1C1917;
            --ink-light:   #57534E;
            --ink-muted:   #A8A29E;
            --amber:       #D97706;
            --amber-light: #FEF3C7;
            --amber-dark:  #B45309;
            --rust:        #C2410C;
            --surface:     #FFFFFF;
            --border:      #E7E5E4;
            --border-dark: #D6D3D1;
            --success:     #065F46;
            --success-bg:  #ECFDF5;
            --error:       #991B1B;
            --error-bg:    #FEF2F2;
            --radius:      12px;
            --radius-sm:   8px;
            --shadow:      0 1px 3px rgba(28,25,23,.06), 0 4px 16px rgba(28,25,23,.08);
            --shadow-lg:   0 8px 40px rgba(28,25,23,.12);
        }

        html { font-size: 16px; -webkit-font-smoothing: antialiased; }

        body {
            font-family: 'DM Sans', sans-serif;
            background-color: var(--cream);
            color: var(--ink);
            min-height: 100vh;
            line-height: 1.6;
        }

        /* ── Typography ────────────────────────────────────────────── */
        .serif { font-family: 'DM Serif Display', serif; }

        h1, h2 { font-family: 'DM Serif Display', serif; font-weight: 400; line-height: 1.2; }

        /* ── Layout ────────────────────────────────────────────────── */
        .auth-wrap {
            min-height: 100vh;
            display: grid;
            grid-template-columns: 1fr 1fr;
        }

        /* ── Left panel — decorative brand side ────────────────────── */
        .auth-panel {
            background-color: var(--ink);
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 3rem;
        }

        .auth-panel::before {
            content: '';
            position: absolute;
            inset: 0;
            background:
                radial-gradient(ellipse 80% 60% at 20% 80%, rgba(217,119,6,.18) 0%, transparent 60%),
                radial-gradient(ellipse 60% 50% at 80% 20%, rgba(194,65,12,.12) 0%, transparent 60%);
        }

        .panel-logo {
            position: relative;
            display: flex;
            align-items: center;
            gap: .6rem;
            text-decoration: none;
        }

        .panel-logo-icon {
            width: 38px; height: 38px;
            background: var(--amber);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
        }

        .panel-logo-icon svg { width: 20px; height: 20px; color: white; }

        .panel-logo-name {
            font-family: 'DM Serif Display', serif;
            font-size: 1.5rem;
            color: #fff;
            letter-spacing: -.01em;
        }

        .panel-content { position: relative; }

        .panel-tagline {
            font-family: 'DM Serif Display', serif;
            font-size: 2.6rem;
            color: #fff;
            line-height: 1.15;
            margin-bottom: 1.5rem;
            letter-spacing: -.02em;
        }

        .panel-tagline em {
            font-style: italic;
            color: #FCD34D;
        }

        .panel-body {
            font-size: .95rem;
            color: #A8A29E;
            line-height: 1.7;
            max-width: 340px;
        }

        .panel-features {
            position: relative;
            display: flex;
            flex-direction: column;
            gap: .75rem;
            margin-top: 2.5rem;
        }

        .panel-feature {
            display: flex;
            align-items: center;
            gap: .75rem;
            font-size: .875rem;
            color: #D6D3D1;
        }

        .panel-feature-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: var(--amber);
            flex-shrink: 0;
        }

        /* ── Right panel — form side ───────────────────────────────── */
        .auth-form-side {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 3rem 2rem;
            background: var(--surface);
            overflow-y: auto;
        }

        .auth-card {
            width: 100%;
            max-width: 420px;
        }

        .auth-heading {
            font-size: 2rem;
            color: var(--ink);
            margin-bottom: .4rem;
        }

        .auth-sub {
            font-size: .925rem;
            color: var(--ink-light);
            margin-bottom: 2rem;
        }

        .auth-sub a {
            color: var(--amber-dark);
            font-weight: 500;
            text-decoration: none;
        }

        .auth-sub a:hover { text-decoration: underline; }

        /* ── Form Elements ─────────────────────────────────────────── */
        .form-group { margin-bottom: 1.1rem; }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        label {
            display: block;
            font-size: .8rem;
            font-weight: 600;
            letter-spacing: .04em;
            text-transform: uppercase;
            color: var(--ink-light);
            margin-bottom: .4rem;
        }

        .input-wrap {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: .875rem;
            top: 50%;
            transform: translateY(-50%);
            width: 16px; height: 16px;
            color: var(--ink-muted);
            pointer-events: none;
            flex-shrink: 0;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="tel"] {
            width: 100%;
            padding: .7rem .875rem .7rem 2.5rem;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            font-family: 'DM Sans', sans-serif;
            font-size: .925rem;
            color: var(--ink);
            background: var(--cream);
            transition: border-color .15s, box-shadow .15s;
            outline: none;
        }

        input:focus {
            border-color: var(--amber);
            box-shadow: 0 0 0 3px rgba(217,119,6,.12);
            background: #fff;
        }

        input.is-error { border-color: var(--rust); }
        input.is-error:focus { box-shadow: 0 0 0 3px rgba(194,65,12,.1); }

        .field-error {
            font-size: .8rem;
            color: var(--rust);
            margin-top: .3rem;
            display: flex;
            align-items: center;
            gap: .3rem;
        }

        /* ── Role Selector ─────────────────────────────────────────── */
        .role-picker {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: .75rem;
            margin-bottom: 1.25rem;
        }

        .role-option { display: none; }

        .role-label {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .5rem;
            padding: 1rem .5rem;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            cursor: pointer;
            transition: border-color .15s, background .15s;
            text-align: center;
            font-size: .85rem;
            color: var(--ink-light);
            font-weight: 500;
        }

        .role-label-icon {
            width: 36px; height: 36px;
            border-radius: 8px;
            background: var(--cream);
            display: flex; align-items: center; justify-content: center;
            transition: background .15s;
        }

        .role-label-icon svg { width: 18px; height: 18px; color: var(--ink-muted); transition: color .15s; }

        .role-option:checked + .role-label {
            border-color: var(--amber);
            background: var(--amber-light);
            color: var(--amber-dark);
        }

        .role-option:checked + .role-label .role-label-icon {
            background: var(--amber);
        }

        .role-option:checked + .role-label .role-label-icon svg { color: #fff; }

        /* ── Remember / Forgot row ─────────────────────────────────── */
        .form-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1.25rem;
            font-size: .85rem;
        }

        .check-label {
            display: flex;
            align-items: center;
            gap: .5rem;
            color: var(--ink-light);
            cursor: pointer;
            text-transform: none;
            letter-spacing: 0;
            font-weight: 400;
            font-size: .85rem;
        }

        input[type="checkbox"] { accent-color: var(--amber); width: 15px; height: 15px; }

        .forgot-link {
            color: var(--amber-dark);
            text-decoration: none;
            font-weight: 500;
        }
        .forgot-link:hover { text-decoration: underline; }

        /* ── Submit button ─────────────────────────────────────────── */
        .btn-primary {
            width: 100%;
            padding: .8rem 1.5rem;
            background: var(--ink);
            color: #fff;
            border: none;
            border-radius: var(--radius-sm);
            font-family: 'DM Sans', sans-serif;
            font-size: .95rem;
            font-weight: 500;
            cursor: pointer;
            transition: background .15s, transform .1s;
            display: flex; align-items: center; justify-content: center; gap: .5rem;
        }

        .btn-primary:hover { background: #292524; }
        .btn-primary:active { transform: scale(.99); }

        .btn-primary svg { width: 16px; height: 16px; }

        /* ── Flash messages ────────────────────────────────────────── */
        .flash {
            padding: .75rem 1rem;
            border-radius: var(--radius-sm);
            font-size: .875rem;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: flex-start;
            gap: .6rem;
        }

        .flash svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 2px; }

        .flash-success { background: var(--success-bg); color: var(--success); }
        .flash-error   { background: var(--error-bg);   color: var(--error);   }

        /* ── Divider ───────────────────────────────────────────────── */
        .divider {
            display: flex; align-items: center; gap: .75rem;
            margin: 1.25rem 0;
            color: var(--ink-muted);
            font-size: .8rem;
        }
        .divider::before, .divider::after {
            content: ''; flex: 1; height: 1px; background: var(--border);
        }

        /* ── Responsive ────────────────────────────────────────────── */
        @media (max-width: 768px) {
            .auth-wrap { grid-template-columns: 1fr; }
            .auth-panel { display: none; }
            .auth-form-side { padding: 2rem 1.25rem; }
        }
    </style>

    @stack('styles')
</head>
<body>

@yield('content')

<script>
    document.addEventListener('DOMContentLoaded', () => {
        feather.replace({ 'stroke-width': 1.75 });
    });
</script>

@stack('scripts')
</body>
</html>
