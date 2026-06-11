<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Provider') — HomeFixer Pro</title>

    {{-- Barlow Condensed (industrial) + Barlow (body) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@400;500;600;700&family=Barlow:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.2/feather.min.js" defer></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --bg:          #0F1117;
            --surface:     #181C27;
            --surface-2:   #1F2435;
            --surface-3:   #252B3B;
            --border:      rgba(255,255,255,.07);
            --border-2:    rgba(255,255,255,.12);
            --teal:        #00C9A7;
            --teal-dim:    rgba(0,201,167,.12);
            --teal-dark:   #008f77;
            --amber:       #F59E0B;
            --amber-dim:   rgba(245,158,11,.12);
            --red:         #EF4444;
            --red-dim:     rgba(239,68,68,.12);
            --green:       #22C55E;
            --green-dim:   rgba(34,197,94,.12);
            --blue:        #3B82F6;
            --blue-dim:    rgba(59,130,246,.12);
            --orange:      #F97316;
            --orange-dim:  rgba(249,115,22,.12);
            --text-1:      #F1F5F9;
            --text-2:      #94A3B8;
            --text-3:      #475569;
            --radius:      12px;
            --radius-sm:   8px;
            --nav-h:       60px;
            --shadow:      0 4px 24px rgba(0,0,0,.4);
        }

        html { font-size: 16px; -webkit-font-smoothing: antialiased; }

        body {
            font-family: 'Barlow', sans-serif;
            background: var(--bg);
            color: var(--text-1);
            min-height: 100vh;
        }

        /* ── Top navigation bar ──────────────────────────────────── */
        .navbar {
            height: var(--nav-h);
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            padding: 0 1.5rem;
            gap: 1rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: .6rem;
            text-decoration: none;
            margin-right: .75rem;
        }

        .brand-mark {
            width: 32px; height: 32px;
            background: var(--teal);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
        }

        .brand-mark svg { width: 16px; height: 16px; color: #000; }

        .brand-name {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--text-1);
            letter-spacing: .02em;
        }

        .brand-pro {
            font-size: .65rem;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--teal);
            background: var(--teal-dim);
            padding: .15rem .45rem;
            border-radius: 4px;
        }

        /* Nav links */
        .nav-links {
            display: flex;
            align-items: center;
            gap: .25rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: .45rem;
            padding: .45rem .85rem;
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-size: .875rem;
            font-weight: 500;
            color: var(--text-2);
            transition: background .15s, color .15s;
            white-space: nowrap;
        }

        .nav-link svg { width: 15px; height: 15px; }

        .nav-link:hover { background: var(--surface-2); color: var(--text-1); }

        .nav-link.active {
            background: var(--teal-dim);
            color: var(--teal);
        }

        .nav-badge {
            background: var(--amber);
            color: #000;
            font-family: 'Barlow Condensed', sans-serif;
            font-size: .7rem;
            font-weight: 700;
            padding: .1rem .4rem;
            border-radius: 99px;
            min-width: 18px;
            text-align: center;
        }

        /* Right side of navbar */
        .nav-right {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        /* Availability toggle */
        .avail-toggle {
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: .4rem .9rem;
            border-radius: 99px;
            border: 1.5px solid var(--border-2);
            font-size: .8rem;
            font-weight: 600;
            font-family: 'Barlow Condensed', sans-serif;
            letter-spacing: .04em;
            cursor: pointer;
            text-decoration: none;
            transition: border-color .15s, background .15s;
        }

        .avail-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
        }

        .avail-toggle.online {
            border-color: rgba(34,197,94,.3);
            color: var(--green);
            background: var(--green-dim);
        }

        .avail-toggle.offline {
            border-color: var(--border-2);
            color: var(--text-2);
        }

        .avail-toggle.online .avail-dot  { background: var(--green); box-shadow: 0 0 6px var(--green); }
        .avail-toggle.offline .avail-dot { background: var(--text-3); }

        /* User chip */
        .user-chip {
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .user-avatar {
            width: 32px; height: 32px;
            border-radius: 8px;
            background: var(--teal);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Barlow Condensed', sans-serif;
            font-size: .85rem;
            font-weight: 700;
            color: #000;
        }

        .user-name-sm {
            font-size: .825rem;
            font-weight: 500;
            color: var(--text-2);
        }

        /* Logout */
        .btn-logout {
            display: flex; align-items: center; gap: .4rem;
            padding: .4rem .75rem;
            border-radius: var(--radius-sm);
            background: transparent;
            border: 1px solid var(--border-2);
            font-family: 'Barlow', sans-serif;
            font-size: .8rem;
            color: var(--text-2);
            cursor: pointer;
            transition: color .15s, border-color .15s;
        }

        .btn-logout:hover { color: var(--red); border-color: var(--red); }
        .btn-logout svg { width: 14px; height: 14px; }

        /* ── Page wrapper ────────────────────────────────────────── */
        .page-wrap {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
        }

        /* ── Shared component styles ─────────────────────────────── */
        .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius);
        }

        .card-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            letter-spacing: .03em;
            color: var(--text-1);
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            padding: .2rem .6rem;
            border-radius: 99px;
            font-family: 'Barlow Condensed', sans-serif;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .badge-amber  { background: var(--amber-dim);  color: var(--amber);  }
        .badge-blue   { background: var(--blue-dim);   color: var(--blue);   }
        .badge-green  { background: var(--green-dim);  color: var(--green);  }
        .badge-red    { background: var(--red-dim);    color: var(--red);    }
        .badge-teal   { background: var(--teal-dim);   color: var(--teal);   }
        .badge-orange { background: var(--orange-dim); color: var(--orange); }
        .badge-gray   { background: rgba(255,255,255,.06); color: var(--text-2); }

        .btn {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .55rem 1.1rem;
            border-radius: var(--radius-sm);
            font-family: 'Barlow', sans-serif;
            font-size: .875rem; font-weight: 600;
            cursor: pointer; border: none; text-decoration: none;
            transition: opacity .15s, transform .1s;
        }

        .btn:active { transform: scale(.98); }
        .btn svg { width: 14px; height: 14px; }

        .btn-teal   { background: var(--teal);   color: #000; }
        .btn-red    { background: var(--red-dim); color: var(--red); border: 1px solid rgba(239,68,68,.3); }
        .btn-ghost  { background: var(--surface-2); color: var(--text-2); border: 1px solid var(--border-2); }
        .btn-teal:hover, .btn-red:hover, .btn-ghost:hover { opacity: .85; }

        /* ── Flash toasts ────────────────────────────────────────── */
        .toast-wrap {
            position: fixed; top: calc(var(--nav-h) + .75rem); right: 1.25rem;
            z-index: 999; display: flex; flex-direction: column; gap: .5rem;
        }

        .toast {
            display: flex; align-items: flex-start; gap: .6rem;
            padding: .75rem 1rem;
            border-radius: var(--radius-sm);
            font-size: .875rem;
            box-shadow: var(--shadow);
            max-width: 340px;
            animation: slideIn .2s ease;
        }

        .toast svg { width: 15px; height: 15px; flex-shrink: 0; margin-top: 1px; }
        .toast-success { background: var(--green-dim); color: var(--green); border: 1px solid rgba(34,197,94,.2); }
        .toast-warning { background: var(--amber-dim); color: var(--amber); border: 1px solid rgba(245,158,11,.2); }
        .toast-error   { background: var(--red-dim);   color: var(--red);   border: 1px solid rgba(239,68,68,.2); }

        @keyframes slideIn {
            from { opacity:0; transform: translateX(16px); }
            to   { opacity:1; transform: translateX(0); }
        }

        /* ── Empty state ─────────────────────────────────────────── */
        .empty-state {
            padding: 3.5rem 1rem;
            text-align: center;
            color: var(--text-3);
        }

        .empty-state svg { width: 44px; height: 44px; margin-bottom: 1rem; opacity: .3; }
        .empty-title { font-family: 'Barlow Condensed', sans-serif; font-size: 1rem; letter-spacing: .03em; color: var(--text-2); margin-bottom: .4rem; }
        .empty-sub   { font-size: .85rem; }
    </style>

    @stack('styles')
</head>
<body>

{{-- ── Navbar ───────────────────────────────────────────────────────────── --}}
<nav class="navbar">
    <a href="{{ route('provider.dashboard') }}" class="nav-brand">
        <span class="brand-mark"><i data-feather="tool"></i></span>
        <span class="brand-name">HomeFixer</span>
        <span class="brand-pro">PRO</span>
    </a>

    <div class="nav-links">
        <a href="{{ route('provider.dashboard') }}"
           class="nav-link {{ request()->routeIs('provider.dashboard') ? 'active' : '' }}">
            <i data-feather="grid"></i> Overview
        </a>

        <a href="{{ route('provider.jobs.available') }}"
           class="nav-link {{ request()->routeIs('provider.jobs.available') ? 'active' : '' }}">
            <i data-feather="inbox"></i> New Offers
            @php $offerCount = \App\Models\JobAssignment::where('provider_id', auth()->id())->where('status','offered')->count(); @endphp
            @if($offerCount > 0)
                <span class="nav-badge">{{ $offerCount }}</span>
            @endif
        </a>

        <a href="{{ route('provider.my-jobs') }}"
           class="nav-link {{ request()->routeIs('provider.my-jobs') ? 'active' : '' }}">
            <i data-feather="briefcase"></i> My Jobs
        </a>
    </div>

    <div class="nav-right">
        {{-- Availability toggle --}}
        @php $profile = auth()->user()->providerProfile; $isOnline = $profile?->is_available ?? false; @endphp
        <form method="POST" action="{{ route('provider.toggle-availability') }}">
            @csrf
            <button type="submit" class="avail-toggle {{ $isOnline ? 'online' : 'offline' }}">
                <span class="avail-dot"></span>
                {{ $isOnline ? 'ONLINE' : 'OFFLINE' }}
            </button>
        </form>

        <div class="user-chip">
            <div class="user-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
            <span class="user-name-sm">{{ explode(' ', auth()->user()->name)[0] }}</span>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">
                <i data-feather="log-out"></i> Logout
            </button>
        </form>
    </div>
</nav>

{{-- Toast flash messages --}}
<div class="toast-wrap">
    @if(session('success'))
        <div class="toast toast-success"><i data-feather="check-circle"></i>{{ session('success') }}</div>
    @endif
    @if(session('warning'))
        <div class="toast toast-warning"><i data-feather="alert-triangle"></i>{{ session('warning') }}</div>
    @endif
    @if(session('error'))
        <div class="toast toast-error"><i data-feather="x-circle"></i>{{ session('error') }}</div>
    @endif
</div>

<div class="page-wrap">
    @yield('content')
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        feather.replace({ 'stroke-width': 1.75 });
        document.querySelectorAll('.toast').forEach(t => {
            setTimeout(() => { t.style.transition='opacity .3s'; t.style.opacity='0'; setTimeout(()=>t.remove(),300); }, 4500);
        });
    });
</script>

@stack('scripts')
</body>
</html>
