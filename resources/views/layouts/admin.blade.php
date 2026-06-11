<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title','Dashboard') — HomeFixer Admin</title>

    {{-- Instrument Serif (editorial display) + Instrument Sans (precise body) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Serif:ital@0;1&family=Instrument+Sans:ital,wght@0,400;0,500;0,600;1,400&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.29.2/feather.min.js" defer></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --slate-950: #020617;
            --slate-900: #0F172A;
            --slate-800: #1E293B;
            --slate-700: #334155;
            --slate-600: #475569;
            --slate-400: #94A3B8;
            --slate-300: #CBD5E1;
            --slate-200: #E2E8F0;
            --slate-100: #F1F5F9;
            --slate-50:  #F8FAFC;
            --white:     #FFFFFF;

            /* Single accent — indigo. Used sparingly. */
            --indigo:      #4F46E5;
            --indigo-dim:  rgba(79,70,229,.1);
            --indigo-border: rgba(79,70,229,.25);

            /* Semantic colours for status chips only */
            --green:  #16A34A; --green-bg:  #F0FDF4; --green-border:  #BBF7D0;
            --amber:  #D97706; --amber-bg:  #FFFBEB; --amber-border:  #FDE68A;
            --red:    #DC2626; --red-bg:    #FEF2F2; --red-border:    #FECACA;
            --blue:   #2563EB; --blue-bg:   #EFF6FF; --blue-border:   #BFDBFE;
            --orange: #EA580C; --orange-bg: #FFF7ED; --orange-border: #FED7AA;

            --sidebar-w: 220px;
            --radius:    10px;
            --radius-sm: 6px;
            --shadow-sm: 0 1px 2px rgba(2,6,23,.06);
            --shadow:    0 1px 3px rgba(2,6,23,.08), 0 4px 16px rgba(2,6,23,.06);
        }

        html { font-size: 15px; -webkit-font-smoothing: antialiased; }

        body {
            font-family: 'Instrument Sans', sans-serif;
            background: var(--slate-100);
            color: var(--slate-900);
            display: flex;
            min-height: 100vh;
        }

        /* ── Sidebar ─────────────────────────────────────────────── */
        .sidebar {
            width: var(--sidebar-w);
            background: var(--slate-950);
            display: flex;
            flex-direction: column;
            position: fixed;
            inset: 0 auto 0 0;
            z-index: 200;
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: .6rem;
            padding: 1.25rem 1rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,.06);
            margin-bottom: .35rem;
            text-decoration: none;
        }

        .logo-icon {
            width: 30px; height: 30px;
            background: var(--white);
            border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }

        .logo-icon svg { width: 15px; height: 15px; color: var(--slate-900); }

        .logo-text {
            display: flex;
            flex-direction: column;
            line-height: 1.1;
        }

        .logo-name {
            font-family: 'Instrument Serif', serif;
            font-size: 1rem;
            color: var(--white);
            letter-spacing: -.01em;
        }

        .logo-sub {
            font-size: .6rem;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--slate-600);
            font-family: 'Instrument Sans', sans-serif;
        }

        .nav-group-label {
            font-size: .6rem;
            font-weight: 600;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--slate-600);
            padding: .6rem 1rem .25rem;
            font-family: 'Instrument Sans', sans-serif;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: .55rem;
            padding: .5rem .85rem;
            margin: .05rem .5rem;
            border-radius: var(--radius-sm);
            text-decoration: none;
            font-size: .825rem;
            font-weight: 500;
            color: var(--slate-400);
            transition: background .12s, color .12s;
        }

        .nav-item svg { width: 15px; height: 15px; flex-shrink: 0; }

        .nav-item:hover { background: rgba(255,255,255,.05); color: var(--slate-200); }

        .nav-item.active {
            background: rgba(255,255,255,.08);
            color: var(--white);
        }

        .nav-item .nav-count {
            margin-left: auto;
            background: var(--indigo);
            color: var(--white);
            font-size: .65rem;
            font-weight: 600;
            padding: .1rem .4rem;
            border-radius: 99px;
            min-width: 18px;
            text-align: center;
        }

        .sidebar-footer {
            margin-top: auto;
            padding: .75rem .5rem;
            border-top: 1px solid rgba(255,255,255,.06);
        }

        .admin-chip {
            display: flex;
            align-items: center;
            gap: .55rem;
            padding: .5rem .65rem;
            border-radius: var(--radius-sm);
        }

        .admin-avatar {
            width: 28px; height: 28px;
            border-radius: 6px;
            background: var(--white);
            display: flex; align-items: center; justify-content: center;
            font-family: 'Instrument Serif', serif;
            font-size: .85rem;
            color: var(--slate-900);
            flex-shrink: 0;
        }

        .admin-info { flex: 1; min-width: 0; }
        .admin-name { font-size: .775rem; color: var(--slate-200); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .admin-role { font-size: .625rem; color: var(--slate-600); letter-spacing: .06em; text-transform: uppercase; }

        .btn-signout {
            display: flex; align-items: center; gap: .4rem;
            padding: .45rem .75rem;
            border-radius: var(--radius-sm);
            background: transparent;
            border: 1px solid rgba(255,255,255,.08);
            font-family: 'Instrument Sans', sans-serif;
            font-size: .75rem; color: var(--slate-500);
            cursor: pointer; width: 100%; margin-top: .35rem;
            transition: color .12s, border-color .12s;
        }

        .btn-signout:hover { color: var(--red); border-color: rgba(220,38,38,.3); }
        .btn-signout svg { width: 13px; height: 13px; }

        /* ── Main ────────────────────────────────────────────────── */
        .main { margin-left: var(--sidebar-w); flex: 1; display: flex; flex-direction: column; min-width: 0; }

        /* ── Topbar ──────────────────────────────────────────────── */
        .topbar {
            background: var(--white);
            border-bottom: 1px solid var(--slate-200);
            height: 52px;
            display: flex;
            align-items: center;
            padding: 0 1.75rem;
            gap: 1rem;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .topbar-title {
            font-family: 'Instrument Serif', serif;
            font-size: 1.05rem;
            color: var(--slate-900);
        }

        .topbar-right { margin-left: auto; display: flex; align-items: center; gap: .6rem; }

        .topbar-date {
            font-size: .75rem;
            color: var(--slate-400);
        }

        /* ── Page content ────────────────────────────────────────── */
        .page { padding: 1.5rem 1.75rem; max-width: 1280px; }

        /* ── Flash toasts ────────────────────────────────────────── */
        .toasts {
            position: fixed; top: 60px; right: 1.25rem;
            z-index: 999; display: flex; flex-direction: column; gap: .4rem;
        }

        .toast {
            display: flex; align-items: flex-start; gap: .55rem;
            padding: .65rem .9rem;
            border-radius: var(--radius-sm);
            font-size: .825rem;
            box-shadow: var(--shadow);
            max-width: 320px;
            border: 1px solid transparent;
            animation: fadeSlide .2s ease;
        }

        .toast svg { width: 14px; height: 14px; flex-shrink: 0; margin-top: 1px; }
        .toast-s { background: var(--green-bg);  color: var(--green);  border-color: var(--green-border); }
        .toast-e { background: var(--red-bg);    color: var(--red);    border-color: var(--red-border);   }
        .toast-w { background: var(--amber-bg);  color: var(--amber);  border-color: var(--amber-border); }

        @keyframes fadeSlide { from { opacity:0; transform:translateX(12px); } to { opacity:1; transform:none; } }

        /* ── Shared components ───────────────────────────────────── */
        .card {
            background: var(--white);
            border: 1px solid var(--slate-200);
            border-radius: var(--radius);
            box-shadow: var(--shadow-sm);
        }

        .card-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .85rem 1.1rem;
            border-bottom: 1px solid var(--slate-200);
        }

        .card-title {
            font-family: 'Instrument Serif', serif;
            font-size: .975rem;
            color: var(--slate-900);
        }

        /* Badges */
        .badge {
            display: inline-flex; align-items: center; gap: .25rem;
            padding: .15rem .55rem;
            border-radius: 99px;
            font-size: .7rem; font-weight: 600;
            border: 1px solid transparent;
            font-family: 'Instrument Sans', sans-serif;
            letter-spacing: .01em;
        }

        .badge svg { width: 10px; height: 10px; }
        .b-green  { background: var(--green-bg);  color: var(--green);  border-color: var(--green-border);  }
        .b-amber  { background: var(--amber-bg);  color: var(--amber);  border-color: var(--amber-border);  }
        .b-red    { background: var(--red-bg);    color: var(--red);    border-color: var(--red-border);    }
        .b-blue   { background: var(--blue-bg);   color: var(--blue);   border-color: var(--blue-border);   }
        .b-indigo { background: var(--indigo-dim);color: var(--indigo); border-color: var(--indigo-border); }
        .b-orange { background: var(--orange-bg); color: var(--orange); border-color: var(--orange-border); }
        .b-slate  { background: var(--slate-100); color: var(--slate-600); border-color: var(--slate-200);  }

        /* Buttons */
        .btn {
            display: inline-flex; align-items: center; gap: .35rem;
            padding: .45rem .9rem;
            border-radius: var(--radius-sm);
            font-family: 'Instrument Sans', sans-serif;
            font-size: .8rem; font-weight: 500;
            cursor: pointer; border: none; text-decoration: none;
            transition: opacity .12s, background .12s;
        }

        .btn svg { width: 13px; height: 13px; }
        .btn:active { opacity: .8; }

        .btn-indigo { background: var(--indigo); color: var(--white); }
        .btn-indigo:hover { opacity: .9; }

        .btn-ghost  { background: var(--slate-100); color: var(--slate-600); border: 1px solid var(--slate-200); }
        .btn-ghost:hover  { background: var(--slate-200); }

        .btn-red    { background: var(--red-bg); color: var(--red); border: 1px solid var(--red-border); }
        .btn-red:hover { background: #fee2e2; }

        .btn-green  { background: var(--green-bg); color: var(--green); border: 1px solid var(--green-border); }
        .btn-green:hover { background: #dcfce7; }

        /* Table */
        table { width: 100%; border-collapse: collapse; font-size: .825rem; }
        thead th {
            padding: .6rem .9rem;
            text-align: left;
            font-size: .675rem; font-weight: 600;
            letter-spacing: .08em; text-transform: uppercase;
            color: var(--slate-400);
            border-bottom: 1px solid var(--slate-200);
            background: var(--slate-50);
            white-space: nowrap;
        }

        tbody tr { border-bottom: 1px solid var(--slate-200); transition: background .1s; }
        tbody tr:last-child { border-bottom: none; }
        tbody tr:hover { background: var(--slate-50); }
        td { padding: .75rem .9rem; vertical-align: middle; color: var(--slate-700); }
        td strong { color: var(--slate-900); font-weight: 500; }

        /* Form inputs (search/filter bars) */
        .filter-bar {
            display: flex; align-items: center; gap: .6rem;
            padding: .75rem 1.1rem;
            border-bottom: 1px solid var(--slate-200);
            flex-wrap: wrap;
        }

        .search-wrap { position: relative; flex: 1; min-width: 180px; }
        .search-icon { position: absolute; left: .65rem; top: 50%; transform: translateY(-50%); width: 13px; height: 13px; color: var(--slate-400); pointer-events: none; }

        input[type="text"].search-input,
        select.filter-select {
            width: 100%;
            padding: .45rem .75rem .45rem 2rem;
            border: 1px solid var(--slate-200);
            border-radius: var(--radius-sm);
            font-family: 'Instrument Sans', sans-serif;
            font-size: .825rem; color: var(--slate-900);
            background: var(--white); outline: none;
            transition: border-color .12s;
        }

        select.filter-select { padding-left: .75rem; cursor: pointer; }
        input[type="text"].search-input:focus,
        select.filter-select:focus { border-color: var(--indigo); }

        /* Pagination */
        .pag-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: .75rem 1.1rem; font-size: .775rem; color: var(--slate-400);
            border-top: 1px solid var(--slate-200);
        }

        .pag-links { display: flex; gap: .25rem; }

        .pg {
            width: 28px; height: 28px;
            display: flex; align-items: center; justify-content: center;
            border-radius: var(--radius-sm); text-decoration: none;
            font-size: .775rem; font-weight: 500;
            color: var(--slate-600); border: 1px solid var(--slate-200);
            transition: border-color .1s, color .1s;
        }

        .pg:hover { border-color: var(--indigo); color: var(--indigo); }
        .pg.on    { background: var(--indigo); color: var(--white); border-color: var(--indigo); }

        /* Empty state */
        .empty { padding: 3rem 1rem; text-align: center; color: var(--slate-400); }
        .empty svg { width: 40px; height: 40px; margin-bottom: .75rem; opacity: .3; }
        .empty-t { font-family: 'Instrument Serif', serif; font-size: .95rem; color: var(--slate-600); margin-bottom: .3rem; }
        .empty-s { font-size: .8rem; }
    </style>

    @stack('styles')
</head>
<body>

{{-- ── Sidebar ──────────────────────────────────────────────────────────── --}}
<aside class="sidebar">
    <a href="{{ route('admin.dashboard') }}" class="sidebar-logo">
        <span class="logo-icon"><i data-feather="home"></i></span>
        <span class="logo-text">
            <span class="logo-name">HomeFixer</span>
            <span class="logo-sub">Admin Console</span>
        </span>
    </a>

    <span class="nav-group-label">Overview</span>
    <a href="{{ route('admin.dashboard') }}"
       class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i data-feather="bar-chart-2"></i> Dashboard
    </a>

    <span class="nav-group-label" style="margin-top:.35rem;">Management</span>
    <a href="{{ route('admin.users') }}"
       class="nav-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
        <i data-feather="users"></i> Users
        @php $unverified = \App\Models\User::where('role','provider')->whereHas('providerProfile', fn($q) => $q->whereNull('verified_at'))->count(); @endphp
        @if($unverified > 0)
            <span class="nav-count">{{ $unverified }}</span>
        @endif
    </a>

    <a href="{{ route('admin.requests') }}"
       class="nav-item {{ request()->routeIs('admin.requests') ? 'active' : '' }}">
        <i data-feather="clipboard"></i> Service Requests
        @php $pending = \App\Models\ServiceRequest::whereIn('status',['pending','recommended'])->count(); @endphp
        @if($pending > 0)
            <span class="nav-count">{{ $pending }}</span>
        @endif
    </a>

    <span class="nav-group-label" style="margin-top:.35rem;">Intelligence</span>
    <a href="{{ route('admin.ai-logs') }}"
       class="nav-item {{ request()->routeIs('admin.ai-logs') ? 'active' : '' }}">
        <i data-feather="cpu"></i> AI Logs
    </a>

    <div class="sidebar-footer">
        <div class="admin-chip">
            <div class="admin-avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
            <div class="admin-info">
                <div class="admin-name">{{ auth()->user()->name }}</div>
                <div class="admin-role">Administrator</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-signout">
                <i data-feather="log-out"></i> Sign out
            </button>
        </form>
    </div>
</aside>

{{-- ── Main ─────────────────────────────────────────────────────────────── --}}
<div class="main">
    <header class="topbar">
        <span class="topbar-title">@yield('page-title', 'Dashboard')</span>
        <div class="topbar-right">
            <span class="topbar-date">{{ now()->format('D, d M Y') }}</span>
        </div>
    </header>

    <div class="toasts">
        @if(session('success'))
            <div class="toast toast-s"><i data-feather="check-circle"></i>{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="toast toast-e"><i data-feather="x-circle"></i>{{ session('error') }}</div>
        @endif
        @if(session('warning'))
            <div class="toast toast-w"><i data-feather="alert-triangle"></i>{{ session('warning') }}</div>
        @endif
    </div>

    <div class="page">
        @yield('content')
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    feather.replace({ 'stroke-width': 1.6 });
    document.querySelectorAll('.toast').forEach(t => {
        setTimeout(() => { t.style.transition='opacity .3s'; t.style.opacity='0'; setTimeout(()=>t.remove(),300); }, 4500);
    });
});
</script>

@stack('scripts')
</body>
</html>
