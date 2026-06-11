@extends('layouts.customer')

@section('title', 'My Dashboard')
@section('page-title', 'Overview')

@push('styles')
<style>
    /* ── Greeting banner ─────────────────────────────────────────── */
    .greeting-banner {
        background: var(--sidebar-bg);
        border-radius: var(--radius);
        padding: 1.75rem 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.75rem;
        position: relative;
        overflow: hidden;
    }

    .greeting-banner::before {
        content: '';
        position: absolute;
        top: -40px; right: -40px;
        width: 220px; height: 220px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(224,123,57,.2) 0%, transparent 70%);
        pointer-events: none;
    }

    .greeting-banner::after {
        content: '';
        position: absolute;
        bottom: -60px; right: 120px;
        width: 160px; height: 160px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(42,125,111,.15) 0%, transparent 70%);
        pointer-events: none;
    }

    .greeting-text { position: relative; }

    .greeting-sup {
        font-size: .75rem;
        color: var(--accent-dim);
        font-family: 'Syne', sans-serif;
        letter-spacing: .08em;
        text-transform: uppercase;
        margin-bottom: .3rem;
    }

    .greeting-name {
        font-family: 'Syne', sans-serif;
        font-size: 1.6rem;
        font-weight: 700;
        color: #fff;
        line-height: 1.2;
        margin-bottom: .4rem;
    }

    .greeting-sub {
        font-size: .875rem;
        color: rgba(255,255,255,.45);
    }

    .greeting-cta {
        position: relative;
        flex-shrink: 0;
    }

    .greeting-cta a {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        padding: .7rem 1.4rem;
        background: var(--accent);
        color: #fff;
        border-radius: var(--radius-sm);
        text-decoration: none;
        font-family: 'Syne', sans-serif;
        font-size: .875rem;
        font-weight: 600;
        transition: opacity .15s;
    }

    .greeting-cta a:hover { opacity: .9; }
    .greeting-cta svg { width: 16px; height: 16px; }

    /* ── Stat cards ──────────────────────────────────────────────── */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.75rem;
    }

    .stat-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.25rem 1.4rem;
        display: flex;
        flex-direction: column;
        gap: .3rem;
        box-shadow: var(--shadow);
        transition: transform .15s, box-shadow .15s;
    }

    .stat-card:hover { transform: translateY(-2px); box-shadow: var(--shadow-md); }

    .stat-icon-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: .4rem;
    }

    .stat-icon {
        width: 38px; height: 38px;
        border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
    }

    .stat-icon svg { width: 18px; height: 18px; }

    .stat-icon-amber { background: var(--amber-light); color: var(--amber); }
    .stat-icon-blue  { background: var(--blue-light);  color: var(--blue);  }
    .stat-icon-teal  { background: var(--teal-light);  color: var(--teal);  }
    .stat-icon-green { background: var(--green-light); color: var(--green); }

    .stat-number {
        font-family: 'Syne', sans-serif;
        font-size: 2rem;
        font-weight: 700;
        color: var(--ink);
        line-height: 1;
    }

    .stat-label {
        font-size: .8rem;
        color: var(--ink-3);
    }

    /* ── Two-column lower section ────────────────────────────────── */
    .lower-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 1.25rem;
    }

    /* ── Recent requests list ────────────────────────────────────── */
    .section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .section-title {
        font-family: 'Syne', sans-serif;
        font-size: 1rem;
        font-weight: 600;
        color: var(--ink);
    }

    .section-link {
        font-size: .8rem;
        color: var(--accent);
        text-decoration: none;
        font-weight: 500;
    }

    .request-list { display: flex; flex-direction: column; gap: .6rem; }

    .request-row {
        display: flex;
        align-items: flex-start;
        gap: .9rem;
        padding: 1rem 1.25rem;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        text-decoration: none;
        color: inherit;
        transition: border-color .15s, box-shadow .15s;
    }

    .request-row:hover { border-color: var(--accent); box-shadow: 0 0 0 3px rgba(224,123,57,.07); }

    .request-dot {
        width: 9px; height: 9px;
        border-radius: 50%;
        flex-shrink: 0;
        margin-top: 5px;
    }

    .request-dot-amber  { background: var(--amber); }
    .request-dot-blue   { background: var(--blue); }
    .request-dot-green  { background: var(--green); }
    .request-dot-teal   { background: var(--teal); }
    .request-dot-red    { background: var(--red); }
    .request-dot-indigo { background: #4F46E5; }
    .request-dot-orange { background: #EA580C; }
    .request-dot-gray   { background: var(--ink-3); }

    .request-info { flex: 1; min-width: 0; }

    .request-title {
        font-size: .9rem;
        font-weight: 500;
        color: var(--ink);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: .2rem;
    }

    .request-meta {
        font-size: .775rem;
        color: var(--ink-3);
        display: flex;
        gap: .75rem;
        align-items: center;
        flex-wrap: wrap;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--ink-3);
    }

    .empty-state svg { width: 40px; height: 40px; margin-bottom: .75rem; color: var(--ink-3); opacity: .4; }
    .empty-title { font-family: 'Syne', sans-serif; font-size: .95rem; color: var(--ink-2); margin-bottom: .4rem; }
    .empty-sub   { font-size: .825rem; }

    /* ── Quick action cards ──────────────────────────────────────── */
    .quick-grid { display: flex; flex-direction: column; gap: .6rem; }

    .quick-card {
        display: flex;
        align-items: center;
        gap: .85rem;
        padding: .9rem 1rem;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        text-decoration: none;
        color: var(--ink);
        transition: border-color .15s, transform .12s;
    }

    .quick-card:hover { border-color: var(--accent); transform: translateX(3px); }

    .quick-icon {
        width: 36px; height: 36px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .quick-icon svg { width: 16px; height: 16px; }

    .quick-label { font-size: .875rem; font-weight: 500; }
    .quick-sub   { font-size: .75rem; color: var(--ink-3); }

    .quick-arrow { margin-left: auto; color: var(--ink-3); }
    .quick-arrow svg { width: 14px; height: 14px; }

    @media (max-width: 960px) {
        .stats-grid  { grid-template-columns: repeat(2, 1fr); }
        .lower-grid  { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

{{-- ── Greeting banner ─────────────────────────────────────────────────── --}}
<div class="greeting-banner">
    <div class="greeting-text">
        <div class="greeting-sup">
            {{ now()->format('l, d M Y') }}
        </div>
        <h1 class="greeting-name">
            @php
                $hour = now()->hour;
                $greet = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
            @endphp
            {{ $greet }}, {{ explode(' ', $user->name)[0] }}
        </h1>
        <p class="greeting-sub">
            @if($stats['in_progress'] > 0)
                You have {{ $stats['in_progress'] }} job{{ $stats['in_progress'] > 1 ? 's' : '' }} in progress.
            @elseif($stats['pending'] > 0)
                You have {{ $stats['pending'] }} request{{ $stats['pending'] > 1 ? 's' : '' }} awaiting action.
            @else
                Everything looks quiet — need help with something at home?
            @endif
        </p>
    </div>
    <div class="greeting-cta">
        <a href="{{ route('customer.request.create') }}">
            <i data-feather="plus-circle"></i>
            Request a service
        </a>
    </div>
</div>

{{-- ── Stat cards ──────────────────────────────────────────────────────── --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon-row">
            <span class="stat-icon stat-icon-amber">
                <i data-feather="clipboard"></i>
            </span>
        </div>
        <div class="stat-number">{{ $stats['total'] }}</div>
        <div class="stat-label">Total requests</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-row">
            <span class="stat-icon stat-icon-blue">
                <i data-feather="clock"></i>
            </span>
        </div>
        <div class="stat-number">{{ $stats['pending'] }}</div>
        <div class="stat-label">Awaiting action</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-row">
            <span class="stat-icon stat-icon-teal">
                <i data-feather="tool"></i>
            </span>
        </div>
        <div class="stat-number">{{ $stats['in_progress'] }}</div>
        <div class="stat-label">In progress</div>
    </div>

    <div class="stat-card">
        <div class="stat-icon-row">
            <span class="stat-icon stat-icon-green">
                <i data-feather="check-circle"></i>
            </span>
        </div>
        <div class="stat-number">{{ $stats['completed'] }}</div>
        <div class="stat-label">Completed</div>
    </div>
</div>

{{-- ── Lower grid ──────────────────────────────────────────────────────── --}}
<div class="lower-grid">

    {{-- Recent requests --}}
    <div>
        <div class="section-head">
            <span class="section-title">Recent Requests</span>
            <a href="{{ route('customer.requests') }}" class="section-link">View all →</a>
        </div>

        @if($recentRequests->isEmpty())
            <div class="card">
                <div class="empty-state">
                    <i data-feather="inbox"></i>
                    <div class="empty-title">No requests yet</div>
                    <p class="empty-sub">Submit your first service request and let AI find the right expert.</p>
                </div>
            </div>
        @else
            <div class="request-list">
                @foreach($recentRequests as $req)
                    @php $badge = $req->status_badge; @endphp
                    <a href="{{ $req->status === 'recommended' ? route('customer.recommendation', $req->id) : route('customer.requests') }}"
                       class="request-row">
                        <span class="request-dot request-dot-{{ $badge['color'] }}"></span>
                        <div class="request-info">
                            <div class="request-title">{{ $req->title }}</div>
                            <div class="request-meta">
                                <span>{{ $req->service?->name ?? 'No service selected' }}</span>
                                <span>·</span>
                                <span>{{ $req->created_at->diffForHumans() }}</span>
                                @if($req->city)
                                    <span>·</span>
                                    <span>{{ $req->city }}</span>
                                @endif
                            </div>
                        </div>
                        <span class="badge badge-{{ $badge['color'] }}">{{ $badge['label'] }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Quick actions --}}
    <div>
        <div class="section-head">
            <span class="section-title">Request by Category</span>
        </div>
        <div class="quick-grid">
            @php
                $iconMap = [
                    'plumbing'   => 'droplet',
                    'electrical' => 'zap',
                    'carpentry'  => 'tool',
                    'cleaning'   => 'wind',
                    'painting'   => 'edit-3',
                    'hvac'       => 'thermometer',
                ];
                $colorMap = [
                    'plumbing'   => ['bg' => 'var(--blue-light)',   'color' => 'var(--blue)'],
                    'electrical' => ['bg' => 'var(--amber-light)',  'color' => 'var(--amber)'],
                    'carpentry'  => ['bg' => '#FEE2CC',             'color' => '#B45309'],
                    'cleaning'   => ['bg' => 'var(--teal-light)',   'color' => 'var(--teal)'],
                    'painting'   => ['bg' => '#FCE7F3',             'color' => '#9D174D'],
                    'hvac'       => ['bg' => '#E0F2FE',             'color' => '#0369A1'],
                ];
            @endphp

            @forelse($categories as $cat)
                @php
                    $icon  = $iconMap[$cat->slug] ?? 'home';
                    $color = $colorMap[$cat->slug] ?? ['bg' => 'var(--bg)', 'color' => 'var(--ink-2)'];
                @endphp
                <a href="{{ route('customer.request.create', ['category' => $cat->slug]) }}"
                   class="quick-card">
                    <span class="quick-icon" style="background:{{ $color['bg'] }}; color:{{ $color['color'] }};">
                        <i data-feather="{{ $icon }}"></i>
                    </span>
                    <div>
                        <div class="quick-label">{{ $cat->name }}</div>
                        <div class="quick-sub">{{ $cat->services->count() }} service{{ $cat->services->count() !== 1 ? 's' : '' }} available</div>
                    </div>
                    <span class="quick-arrow"><i data-feather="chevron-right"></i></span>
                </a>
            @empty
                <p style="font-size:.85rem; color:var(--ink-3); padding:.5rem 0;">No categories available.</p>
            @endforelse
        </div>
    </div>

</div>

@endsection
