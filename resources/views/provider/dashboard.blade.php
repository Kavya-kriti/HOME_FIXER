@extends('layouts.provider')

@section('title', 'Provider Overview')

@push('styles')
<style>
    /* ── Page header ─────────────────────────────────────────────── */
    .page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 1.75rem;
        gap: 1rem;
    }

    .page-title {
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 1.75rem;
        font-weight: 700;
        letter-spacing: .02em;
        color: var(--text-1);
        line-height: 1.1;
    }

    .page-sub {
        font-size: .875rem;
        color: var(--text-2);
        margin-top: .3rem;
    }

    /* ── Verification banner ─────────────────────────────────────── */
    .verify-banner {
        display: flex;
        align-items: center;
        gap: .9rem;
        padding: .9rem 1.2rem;
        background: var(--amber-dim);
        border: 1px solid rgba(245,158,11,.2);
        border-radius: var(--radius);
        margin-bottom: 1.5rem;
        font-size: .875rem;
        color: var(--amber);
    }

    .verify-banner svg { width: 18px; height: 18px; flex-shrink: 0; }

    /* ── Stat cards ──────────────────────────────────────────────── */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.25rem;
        position: relative;
        overflow: hidden;
        transition: border-color .2s;
    }

    .stat-card:hover { border-color: var(--border-2); }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 2px;
    }

    .stat-card.c-teal::before   { background: var(--teal); }
    .stat-card.c-amber::before  { background: var(--amber); }
    .stat-card.c-orange::before { background: var(--orange); }
    .stat-card.c-green::before  { background: var(--green); }

    .stat-icon {
        width: 36px; height: 36px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        margin-bottom: .85rem;
    }

    .stat-icon svg { width: 17px; height: 17px; }

    .stat-icon-teal   { background: var(--teal-dim);   color: var(--teal);   }
    .stat-icon-amber  { background: var(--amber-dim);  color: var(--amber);  }
    .stat-icon-orange { background: var(--orange-dim); color: var(--orange); }
    .stat-icon-green  { background: var(--green-dim);  color: var(--green);  }

    .stat-num {
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 2.2rem;
        font-weight: 700;
        color: var(--text-1);
        line-height: 1;
        margin-bottom: .25rem;
    }

    .stat-label { font-size: .8rem; color: var(--text-2); }

    /* ── Two-column grid ─────────────────────────────────────────── */
    .main-grid {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: 1.25rem;
        align-items: start;
    }

    /* ── New offers section ──────────────────────────────────────── */
    .offer-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        padding: 1.1rem 1.25rem;
        margin-bottom: .75rem;
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 1rem;
        align-items: center;
        transition: border-color .15s;
        position: relative;
    }

    .offer-card:hover { border-color: var(--border-2); }

    .offer-card.urgent { border-left: 3px solid var(--amber); }

    .offer-time-flag {
        position: absolute;
        top: .75rem; right: .75rem;
        font-size: .7rem;
        color: var(--amber);
        font-family: 'Barlow Condensed', sans-serif;
        letter-spacing: .05em;
        display: flex; align-items: center; gap: .25rem;
    }

    .offer-time-flag svg { width: 11px; height: 11px; }

    .offer-title {
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 1.05rem;
        font-weight: 600;
        letter-spacing: .02em;
        color: var(--text-1);
        margin-bottom: .25rem;
    }

    .offer-meta {
        display: flex;
        flex-wrap: wrap;
        gap: .6rem;
        font-size: .8rem;
        color: var(--text-2);
        align-items: center;
        margin-bottom: .6rem;
    }

    .offer-meta-item {
        display: flex; align-items: center; gap: .25rem;
    }

    .offer-meta-item svg { width: 12px; height: 12px; }

    .offer-desc {
        font-size: .82rem;
        color: var(--text-2);
        line-height: 1.5;
        margin-bottom: .75rem;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .offer-actions { display: flex; gap: .5rem; }

    /* ── Recent activity list ────────────────────────────────────── */
    .activity-row {
        display: flex;
        align-items: flex-start;
        gap: .85rem;
        padding: .85rem 1.25rem;
        border-bottom: 1px solid var(--border);
        transition: background .12s;
    }

    .activity-row:last-child { border-bottom: none; }
    .activity-row:hover { background: var(--surface-2); }

    .activity-icon {
        width: 32px; height: 32px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .activity-icon svg { width: 14px; height: 14px; }

    .activity-title {
        font-size: .875rem;
        font-weight: 500;
        color: var(--text-1);
        margin-bottom: .2rem;
    }

    .activity-sub {
        font-size: .775rem;
        color: var(--text-2);
    }

    .activity-time {
        margin-left: auto;
        font-size: .75rem;
        color: var(--text-3);
        white-space: nowrap;
        flex-shrink: 0;
    }

    /* ── Profile card (right sidebar) ───────────────────────────── */
    .profile-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        overflow: hidden;
    }

    .profile-header {
        background: var(--surface-2);
        padding: 1.5rem 1.25rem;
        text-align: center;
        border-bottom: 1px solid var(--border);
        position: relative;
    }

    .profile-avatar {
        width: 64px; height: 64px;
        border-radius: 14px;
        background: var(--teal);
        display: flex; align-items: center; justify-content: center;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 1.5rem;
        font-weight: 700;
        color: #000;
        margin: 0 auto .75rem;
    }

    .profile-name {
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 1.15rem;
        font-weight: 700;
        letter-spacing: .02em;
        color: var(--text-1);
        margin-bottom: .2rem;
    }

    .profile-role {
        font-size: .75rem;
        color: var(--text-2);
        text-transform: uppercase;
        letter-spacing: .08em;
    }

    .profile-body { padding: 1rem 1.25rem; }

    .profile-stat-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: .55rem 0;
        border-bottom: 1px solid var(--border);
        font-size: .85rem;
    }

    .profile-stat-row:last-child { border-bottom: none; }

    .profile-stat-key { color: var(--text-2); }

    .profile-stat-val {
        font-weight: 600;
        color: var(--text-1);
        font-family: 'Barlow Condensed', sans-serif;
        font-size: .95rem;
        letter-spacing: .02em;
    }

    /* Star rating */
    .stars-row {
        display: flex; align-items: center; gap: 2px;
    }

    .stars-row svg {
        width: 12px; height: 12px;
        fill: var(--amber); color: var(--amber);
    }

    .stars-row .empty { fill: none; color: var(--border-2); }

    @media (max-width: 1000px) {
        .stats-row  { grid-template-columns: repeat(2,1fr); }
        .main-grid  { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

{{-- Page header --}}
<div class="page-header">
    <div>
        <h1 class="page-title">
    Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 17 ? 'afternoon' : 'evening') }},
    {{ explode(' ', $user->name)[0] }}
</h1>
        <p class="page-sub">{{ now()->format('l, d M Y') }} &mdash; Here's your workday at a glance</p>
    </div>
    @if($stats['offered'] > 0)
        <a href="{{ route('provider.jobs.available') }}" class="btn btn-teal">
            <i data-feather="inbox"></i>
            {{ $stats['offered'] }} new offer{{ $stats['offered'] > 1 ? 's' : '' }} waiting
        </a>
    @endif
</div>

{{-- Verification banner (if not verified yet) --}}
@if($profile && !$profile->isVerified())
    <div class="verify-banner">
        <i data-feather="shield"></i>
        <span>
            <strong>Account pending verification.</strong>
            Your profile is under review by our admin team. You will receive job offers once approved.
        </span>
    </div>
@endif

{{-- Stat cards --}}
<div class="stats-row">
    <div class="stat-card c-amber">
        <div class="stat-icon stat-icon-amber"><i data-feather="inbox"></i></div>
        <div class="stat-num">{{ $stats['offered'] }}</div>
        <div class="stat-label">New offers</div>
    </div>
    <div class="stat-card c-orange">
        <div class="stat-icon stat-icon-orange"><i data-feather="tool"></i></div>
        <div class="stat-num">{{ $stats['active'] }}</div>
        <div class="stat-label">Active jobs</div>
    </div>
    <div class="stat-card c-teal">
        <div class="stat-icon stat-icon-teal"><i data-feather="check-circle"></i></div>
        <div class="stat-num">{{ $stats['completed'] }}</div>
        <div class="stat-label">Completed</div>
    </div>
    <div class="stat-card c-green">
        <div class="stat-icon stat-icon-green"><i data-feather="trending-up"></i></div>
        <div class="stat-num">₹{{ number_format($stats['earnings'], 0) }}</div>
        <div class="stat-label">Total earnings</div>
    </div>
</div>

{{-- Main content grid --}}
<div class="main-grid">
    <div>

        {{-- New job offers --}}
        <div class="card-header" style="margin-bottom:.75rem; padding:0;">
            <span class="card-title" style="font-size:1.1rem;">
                New Job Offers
                @if($newOffers->count() > 0)
                    <span class="badge badge-amber" style="margin-left:.5rem;">{{ $newOffers->count() }}</span>
                @endif
            </span>
            <a href="{{ route('provider.jobs.available') }}" style="font-size:.8rem; color:var(--teal); text-decoration:none;">
                View all →
            </a>
        </div>

        @forelse($newOffers->take(3) as $offer)
            @php
                $req     = $offer->serviceRequest;
                $isNew   = $offer->created_at->gt(now()->subHours(2));
            @endphp
            <div class="offer-card {{ $isNew ? 'urgent' : '' }}">
                @if($isNew)
                    <div class="offer-time-flag">
                        <i data-feather="clock"></i>
                        {{ $offer->created_at->diffForHumans() }}
                    </div>
                @endif

                <div>
                    <div class="offer-title">{{ $req->title }}</div>
                    <div class="offer-meta">
                        @if($req->service)
                            <span class="badge badge-teal">{{ $req->service->name }}</span>
                        @endif
                        <span class="offer-meta-item">
                            <i data-feather="user"></i> {{ $req->customer->name }}
                        </span>
                        <span class="offer-meta-item">
                            <i data-feather="map-pin"></i> {{ $req->city }}, {{ $req->pincode }}
                        </span>
                        @if($req->budget_max)
                            <span class="offer-meta-item">
                                <i data-feather="dollar-sign"></i> Up to ₹{{ number_format($req->budget_max) }}
                            </span>
                        @endif
                        @if($req->preferred_date)
                            <span class="offer-meta-item">
                                <i data-feather="calendar"></i> {{ \Carbon\Carbon::parse($req->preferred_date)->format('d M') }}
                            </span>
                        @endif
                    </div>
                    <p class="offer-desc">{{ $req->description }}</p>

                    <div class="offer-actions">
                        {{-- Accept --}}
                        <form method="POST" action="{{ route('provider.jobs.accept', $offer->id) }}" style="display:inline;">
                            @csrf
                            <button type="submit" class="btn btn-teal">
                                <i data-feather="check"></i> Accept
                            </button>
                        </form>

                        {{-- Decline --}}
                        <form method="POST" action="{{ route('provider.jobs.reject', $offer->id) }}" style="display:inline;"
                              onsubmit="return confirm('Decline this job offer?')">
                            @csrf
                            <button type="submit" class="btn btn-red">
                                <i data-feather="x"></i> Decline
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Score chip if available from AI payload --}}
                @php
                    $aiScore = null;
                    $payload = $req->ai_recommendation_payload;
                    if ($payload) {
                        foreach (($payload['top_providers'] ?? []) as $p) {
                            if ($p['provider_id'] === $user->id) {
                                $aiScore = $p['score'] ?? null;
                                break;
                            }
                        }
                    }
                @endphp

                @if($aiScore)
                    <div style="text-align:center; min-width:80px;">
                        <div style="font-family:'Barlow Condensed',sans-serif; font-size:1.8rem; font-weight:700; color:var(--teal); line-height:1;">
                            {{ round($aiScore * 100) }}%
                        </div>
                        <div style="font-size:.65rem; letter-spacing:.08em; color:var(--text-3); text-transform:uppercase;">
                            AI Match
                        </div>
                        <div style="height:3px; background:var(--surface-3); border-radius:99px; margin-top:.4rem; overflow:hidden;">
                            <div style="height:100%; width:{{ round($aiScore*100) }}%; background:var(--teal); border-radius:99px;"></div>
                        </div>
                    </div>
                @endif
            </div>
        @empty
            <div class="card">
                <div class="empty-state">
                    <i data-feather="inbox"></i>
                    <div class="empty-title">No new offers right now</div>
                    <p class="empty-sub">New job offers will appear here when customers select you.</p>
                </div>
            </div>
        @endforelse

        {{-- Recent activity --}}
        <div style="margin-top:1.5rem;">
            <div class="card-header" style="margin-bottom:.75rem; padding:0;">
                <span class="card-title" style="font-size:1.1rem;">Recent Activity</span>
                <a href="{{ route('provider.my-jobs') }}" style="font-size:.8rem; color:var(--teal); text-decoration:none;">
                    All jobs →
                </a>
            </div>

            @if($recentJobs->isEmpty())
                <div class="card">
                    <div class="empty-state">
                        <i data-feather="briefcase"></i>
                        <div class="empty-title">No activity yet</div>
                        <p class="empty-sub">Your completed and active jobs will appear here.</p>
                    </div>
                </div>
            @else
                <div class="card" style="overflow:hidden;">
                    @foreach($recentJobs as $job)
                        @php
                            $badge = $job->status_badge;
                            $iconMap = ['offered'=>'inbox','accepted'=>'check','started'=>'tool','done'=>'check-circle','rejected'=>'x-circle'];
                            $colorMap = ['offered'=>'amber','accepted'=>'blue','started'=>'orange','done'=>'teal','rejected'=>'red'];
                            $ic = $iconMap[$job->status] ?? 'circle';
                            $cl = $colorMap[$job->status] ?? 'gray';
                        @endphp
                        <div class="activity-row">
                            <span class="activity-icon" style="background:var(--{{ $cl }}-dim); color:var(--{{ $cl }});">
                                <i data-feather="{{ $ic }}"></i>
                            </span>
                            <div style="min-width:0; flex:1;">
                                <div class="activity-title">{{ Str::limit($job->serviceRequest->title, 50) }}</div>
                                <div class="activity-sub">
                                    {{ $job->serviceRequest->customer->name }} &middot;
                                    {{ $job->serviceRequest->city }}
                                    @if($job->quoted_price)
                                        &middot; ₹{{ number_format($job->quoted_price) }}
                                    @endif
                                </div>
                            </div>
                            <div style="display:flex; flex-direction:column; align-items:flex-end; gap:.3rem;">
                                <span class="badge badge-{{ $badge['color'] }}">{{ $badge['label'] }}</span>
                                <span class="activity-time">{{ $job->updated_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- ── Profile sidebar ────────────────────────────────────── --}}
    <div>
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    {{ strtoupper(substr($user->name, 0, 2)) }}
                </div>
                <div class="profile-name">{{ $user->name }}</div>
                <div class="profile-role">Service Provider</div>
                @if($profile?->isVerified())
                    <div style="margin-top:.5rem;">
                        <span class="badge badge-teal"><i data-feather="shield" style="width:10px;height:10px;"></i> Verified</span>
                    </div>
                @endif
            </div>

            <div class="profile-body">
                @if($profile)
                    <div class="profile-stat-row">
                        <span class="profile-stat-key">Rating</span>
                        <div style="display:flex; align-items:center; gap:.5rem;">
                            <div class="stars-row">
                                @for($s=1;$s<=5;$s++)
                                    <svg viewBox="0 0 24 24" class="{{ $s<=round($profile->avg_rating)?'':'empty' }}">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                    </svg>
                                @endfor
                            </div>
                            <span class="profile-stat-val">{{ number_format($profile->avg_rating, 1) }}</span>
                        </div>
                    </div>
                    <div class="profile-stat-row">
                        <span class="profile-stat-key">Jobs done</span>
                        <span class="profile-stat-val">{{ $profile->total_jobs }}</span>
                    </div>
                    <div class="profile-stat-row">
                        <span class="profile-stat-key">Experience</span>
                        <span class="profile-stat-val">{{ $profile->years_experience }} yrs</span>
                    </div>
                    <div class="profile-stat-row">
                        <span class="profile-stat-key">Hourly rate</span>
                        <span class="profile-stat-val">
                            {{ $profile->hourly_rate ? '₹'.number_format($profile->hourly_rate) : '—' }}
                        </span>
                    </div>
                    <div class="profile-stat-row">
                        <span class="profile-stat-key">Service radius</span>
                        <span class="profile-stat-val">{{ $profile->service_radius_km }} km</span>
                    </div>
                    <div class="profile-stat-row">
                        <span class="profile-stat-key">Status</span>
                        <span class="badge {{ $profile->is_available ? 'badge-green' : 'badge-gray' }}">
                            {{ $profile->is_available ? 'Online' : 'Offline' }}
                        </span>
                    </div>
                @else
                    <p style="font-size:.85rem; color:var(--text-2); padding:.5rem 0;">
                        Profile not set up yet.
                    </p>
                @endif
            </div>
        </div>

        {{-- Quick tips --}}
        <div style="margin-top:1rem; background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); padding:1rem 1.2rem;">
            <div style="font-family:'Barlow Condensed',sans-serif; font-size:.9rem; font-weight:700; letter-spacing:.03em; margin-bottom:.6rem; color:var(--text-1);">
                How to get more jobs
            </div>
            <ul style="font-size:.8rem; color:var(--text-2); line-height:2; padding-left:1rem;">
                <li>Keep your status <span style="color:var(--green);">Online</span> during working hours</li>
                <li>Accept offers quickly — response time affects ranking</li>
                <li>Complete jobs on time to improve your rating</li>
                <li>Ask customers to leave a review after the job</li>
            </ul>
        </div>
    </div>
</div>

@endsection
