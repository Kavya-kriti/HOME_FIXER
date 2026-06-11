<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — HomeFixer</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700&family=Epilogue:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.2/feather.min.js" defer></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:          #F4F1EC;       /* warm parchment */
            --surface:     #FDFCFA;
            --sidebar-bg:  #1A1714;       /* near-black warm */
            --sidebar-w:   240px;
            --ink:         #1A1714;
            --ink-2:       #5C5754;
            --ink-3:       #A39E9B;
            --accent:      #E07B39;       /* terracotta */
            --accent-dim:  #F5C9A8;
            --accent-dark: #B85A1E;
            --teal:        #2A7D6F;
            --teal-light:  #D0EDE9;
            --blue:        #2563EB;
            --blue-light:  #DBEAFE;
            --green:       #15803D;
            --green-light: #DCFCE7;
            --red:         #B91C1C;
            --red-light:   #FEE2E2;
            --amber:       #B45309;
            --amber-light: #FEF3C7;
            --border:      #E5E1DA;
            --radius:      14px;
            --radius-sm:   8px;
            --shadow:      0 1px 3px rgba(26,23,20,.07), 0 4px 16px rgba(26,23,20,.06);
            --shadow-md:   0 4px 24px rgba(26,23,20,.10);
        }

        html { font-size: 16px; -webkit-font-smoothing: antialiased; }

        body {
            font-family: 'Epilogue', sans-serif;
            background: var(--bg);
            color: var(--ink);
            display: flex;
            min-height: 100vh;
        }

        /* ── Sidebar ─────────────────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 100;
            overflow: hidden;
        }

        .sidebar::before {
            content: '';
            position: absolute;
            bottom: -60px; right: -60px;
            width: 200px; height: 200px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(224,123,57,.12) 0%, transparent 70%);
            pointer-events: none;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: 1.5rem 1.25rem 1rem;
            text-decoration: none;
            border-bottom: 1px solid rgba(255,255,255,.07);
            margin-bottom: .5rem;
        }

        .logo-mark {
            width: 34px; height: 34px;
            background: var(--accent);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .logo-mark svg { width: 18px; height: 18px; color: #fff; }

        .logo-name {
            font-family: 'Syne', sans-serif;
            font-size: 1.2rem;
            font-weight: 700;
            color: #fff;
            letter-spacing: -.01em;
        }

        .nav-section-label {
            font-family: 'Syne', sans-serif;
            font-size: .65rem;
            font-weight: 600;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: rgba(255,255,255,.28);
            padding: .75rem 1.25rem .3rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: .7rem;
            padding: .65rem 1.25rem;
            margin: .1rem .75rem;
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-size: .875rem;
            font-weight: 400;
            color: rgba(255,255,255,.55);
            transition: background .15s, color .15s;
            position: relative;
        }

        .nav-link svg { width: 16px; height: 16px; flex-shrink: 0; }

        .nav-link:hover {
            background: rgba(255,255,255,.07);
            color: rgba(255,255,255,.9);
        }

        .nav-link.active {
            background: var(--accent);
            color: #fff;
            font-weight: 500;
        }

        .nav-link .badge {
            margin-left: auto;
            background: rgba(255,255,255,.15);
            color: #fff;
            font-size: .7rem;
            font-family: 'Syne', sans-serif;
            font-weight: 600;
            padding: .15rem .45rem;
            border-radius: 99px;
            min-width: 20px;
            text-align: center;
        }

        .nav-link.active .badge { background: rgba(255,255,255,.25); }

        .sidebar-footer {
            margin-top: auto;
            padding: 1rem .75rem;
            border-top: 1px solid rgba(255,255,255,.07);
        }

        .user-chip {
            display: flex;
            align-items: center;
            gap: .65rem;
            padding: .6rem .75rem;
            border-radius: var(--radius-sm);
            cursor: pointer;
        }

        .user-avatar {
            width: 32px; height: 32px;
            border-radius: 50%;
            background: var(--accent);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Syne', sans-serif;
            font-size: .8rem;
            font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }

        .user-name {
            font-size: .82rem;
            color: rgba(255,255,255,.8);
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role-tag {
            font-size: .68rem;
            color: rgba(255,255,255,.35);
        }

        /* ── Main area ───────────────────────────────────────────── */
        .main {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        /* ── Top bar ─────────────────────────────────────────────── */
        .topbar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 2rem;
            height: 60px;
            gap: 1rem;
            position: sticky;
            top: 0;
            z-index: 50;
        }

        .topbar-title {
            font-family: 'Syne', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            color: var(--ink);
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin-left: auto;
        }

        .icon-btn {
            width: 36px; height: 36px;
            border-radius: 8px;
            border: 1.5px solid var(--border);
            background: transparent;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            color: var(--ink-2);
            transition: border-color .15s, color .15s;
            position: relative;
            text-decoration: none;
        }

        .icon-btn:hover { border-color: var(--accent); color: var(--accent); }
        .icon-btn svg { width: 16px; height: 16px; }

        .notif-dot {
            position: absolute;
            top: 5px; right: 5px;
            width: 7px; height: 7px;
            background: var(--accent);
            border-radius: 50%;
            border: 1.5px solid var(--surface);
        }

        /* ── Page content ────────────────────────────────────────── */
        .page-content {
            padding: 2rem;
            flex: 1;
            max-width: 1100px;
            width: 100%;
        }

        /* ── Flash toasts ────────────────────────────────────────── */
        .toast-wrap {
            position: fixed;
            top: 1.25rem; right: 1.25rem;
            z-index: 999;
            display: flex;
            flex-direction: column;
            gap: .5rem;
        }

        .toast {
            display: flex;
            align-items: flex-start;
            gap: .6rem;
            padding: .75rem 1rem;
            border-radius: var(--radius-sm);
            font-size: .875rem;
            box-shadow: var(--shadow-md);
            max-width: 340px;
            animation: slideIn .25s ease;
        }

        .toast svg { width: 16px; height: 16px; flex-shrink: 0; margin-top: 1px; }

        .toast-success { background: var(--green-light); color: var(--green); }
        .toast-warning { background: var(--amber-light); color: var(--amber); }
        .toast-error   { background: var(--red-light);   color: var(--red);   }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(16px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        /* ── Shared component styles ─────────────────────────────── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .6rem 1.2rem;
            border-radius: var(--radius-sm);
            font-family: 'Syne', sans-serif;
            font-size: .85rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: opacity .15s, transform .1s;
        }

        .btn:active { transform: scale(.98); }
        .btn svg { width: 15px; height: 15px; }

        .btn-accent {
            background: var(--accent);
            color: #fff;
        }
        .btn-accent:hover { opacity: .9; }

        .btn-ghost {
            background: transparent;
            color: var(--ink-2);
            border: 1.5px solid var(--border);
        }
        .btn-ghost:hover { border-color: var(--accent); color: var(--accent); }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            padding: .2rem .6rem;
            border-radius: 99px;
            font-size: .72rem;
            font-family: 'Syne', sans-serif;
            font-weight: 600;
            letter-spacing: .01em;
        }

        .badge-amber  { background: var(--amber-light);  color: var(--amber);  }
        .badge-blue   { background: var(--blue-light);   color: var(--blue);   }
        .badge-green  { background: var(--green-light);  color: var(--green);  }
        .badge-red    { background: var(--red-light);    color: var(--red);    }
        .badge-teal   { background: var(--teal-light);   color: var(--teal);   }
        .badge-indigo { background: #E0E7FF; color: #3730A3; }
        .badge-orange { background: #FFEDD5; color: #C2410C; }
        .badge-gray   { background: #F3F4F6; color: #374151; }

        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main { margin-left: 0; }
        }
    </style>

    @stack('styles')
</head>
<body>

{{-- ── Sidebar ──────────────────────────────────────────────────────────── --}}
<aside class="sidebar">
    <a href="{{ route('customer.dashboard') }}" class="sidebar-logo">
        <span class="logo-mark">
            <i data-feather="home"></i>
        </span>
        <span class="logo-name">HomeFixer</span>
    </a>

    <span class="nav-section-label">Main</span>

    <a href="{{ route('customer.dashboard') }}"
       class="nav-link {{ request()->routeIs('customer.dashboard') ? 'active' : '' }}">
        <i data-feather="grid"></i>
        Overview
    </a>

    <a href="{{ route('customer.request.create') }}"
       class="nav-link {{ request()->routeIs('customer.request.*') ? 'active' : '' }}">
        <i data-feather="plus-circle"></i>
        New Request
    </a>

    <a href="{{ route('customer.requests') }}"
       class="nav-link {{ request()->routeIs('customer.requests') ? 'active' : '' }}">
        <i data-feather="list"></i>
        My Requests
        @php $pending = auth()->user()->serviceRequests()->whereIn('status',['pending','recommended'])->count() @endphp
        @if($pending > 0)
            <span class="badge">{{ $pending }}</span>
        @endif
    </a>

    <span class="nav-section-label" style="margin-top:.5rem;">Account</span>

    <a href="#" class="nav-link">
        <i data-feather="star"></i>
        My Reviews
    </a>

    <a href="#" class="nav-link">
        <i data-feather="settings"></i>
        Settings
    </a>

    <div class="sidebar-footer">
        <div class="user-chip">
            <div class="user-avatar">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div style="min-width:0;">
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role-tag">Customer</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}" style="margin-top:.4rem;">
            @csrf
            <button type="submit" class="nav-link" style="width:100%; border:none; cursor:pointer; background:none;">
                <i data-feather="log-out"></i>
                Sign out
            </button>
        </form>
    </div>
</aside>

{{-- ── Main ─────────────────────────────────────────────────────────────── --}}
<div class="main">

    {{-- Top bar --}}
    <header class="topbar">
        <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
        <div class="topbar-right">
            <a href="{{ route('customer.request.create') }}" class="btn btn-accent" style="height:36px; padding:.4rem 1rem;">
                <i data-feather="plus"></i>
                New Request
            </a>
            <button class="icon-btn" title="Notifications">
                <i data-feather="bell"></i>
                <span class="notif-dot"></span>
            </button>
        </div>
    </header>

    {{-- Flash toasts --}}
    <div class="toast-wrap">
        @if(session('success'))
            <div class="toast toast-success">
                <i data-feather="check-circle"></i>
                {{ session('success') }}
            </div>
        @endif
        @if(session('warning'))
            <div class="toast toast-warning">
                <i data-feather="alert-triangle"></i>
                {{ session('warning') }}
            </div>
        @endif
        @if(session('error'))
            <div class="toast toast-error">
                <i data-feather="x-circle"></i>
                {{ session('error') }}
            </div>
        @endif
    </div>

    <div class="page-content">
        @yield('content')
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        feather.replace({ 'stroke-width': 1.75 });

        // Auto-dismiss toasts after 4 seconds
        document.querySelectorAll('.toast').forEach(t => {
            setTimeout(() => {
                t.style.transition = 'opacity .3s';
                t.style.opacity = '0';
                setTimeout(() => t.remove(), 300);
            }, 4000);
        });
    });
</script>

@stack('scripts')
</body>
</html>
