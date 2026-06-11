@extends('layouts.customer')

@section('title', 'AI Recommendation Results')
@section('page-title', 'AI Recommendation')

@push('styles')
<style>
    .result-header {
        background: var(--sidebar-bg);
        border-radius: var(--radius);
        padding: 1.5rem 1.75rem;
        display: flex;
        align-items: center;
        gap: 1.25rem;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .result-header::before {
        content: '';
        position: absolute;
        top: -40px; right: -20px;
        width: 180px; height: 180px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(224,123,57,.18) 0%, transparent 70%);
        pointer-events: none;
    }

    .ai-pulse {
        width: 52px; height: 52px;
        border-radius: 14px;
        background: var(--accent);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        position: relative;
    }

    .ai-pulse::after {
        content: '';
        position: absolute;
        inset: -4px;
        border-radius: 18px;
        border: 2px solid rgba(224,123,57,.35);
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: .4; transform: scale(1.1); }
    }

    .ai-pulse svg { width: 24px; height: 24px; color: #fff; }

    .result-header-text { position: relative; }

    .result-title {
        font-family: 'Syne', sans-serif;
        font-size: 1.25rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: .3rem;
    }

    .result-sub { font-size: .875rem; color: rgba(255,255,255,.5); }

    .result-meta {
        margin-left: auto;
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: .3rem;
        position: relative;
        flex-shrink: 0;
    }

    .confidence-pill {
        display: flex;
        align-items: center;
        gap: .4rem;
        background: rgba(255,255,255,.08);
        border: 1px solid rgba(255,255,255,.12);
        border-radius: 99px;
        padding: .3rem .85rem;
        font-family: 'Syne', sans-serif;
        font-size: .8rem;
        color: rgba(255,255,255,.8);
    }

    .confidence-dot {
        width: 7px; height: 7px;
        border-radius: 50%;
        background: #4ADE80;
    }

    /* ── Two-col layout ──────────────────────────────────────────── */
    .result-grid {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: 1.25rem;
        align-items: start;
    }

    /* ── Recommended service box ─────────────────────────────────── */
    .service-box {
        background: rgba(224,123,57,.07);
        border: 1.5px solid rgba(224,123,57,.25);
        border-radius: var(--radius);
        padding: 1.1rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.25rem;
    }

    .service-box-icon {
        width: 44px; height: 44px;
        border-radius: 10px;
        background: var(--accent);
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .service-box-icon svg { width: 20px; height: 20px; color: #fff; }

    .service-box-label { font-size: .72rem; font-family: 'Syne', sans-serif; letter-spacing: .08em; text-transform: uppercase; color: var(--ink-3); }
    .service-box-name  { font-family: 'Syne', sans-serif; font-size: 1rem; font-weight: 700; color: var(--ink); }

    /* ── Provider cards ──────────────────────────────────────────── */
    .providers-title {
        font-family: 'Syne', sans-serif;
        font-size: .9rem;
        font-weight: 600;
        color: var(--ink);
        margin-bottom: .75rem;
    }

    .provider-cards { display: flex; flex-direction: column; gap: .75rem; }

    .provider-card {
        background: var(--surface);
        border: 1.5px solid var(--border);
        border-radius: var(--radius);
        padding: 1.25rem;
        display: grid;
        grid-template-columns: auto 1fr auto;
        gap: 1rem;
        align-items: center;
        transition: border-color .15s, box-shadow .15s;
        position: relative;
    }

    .provider-card.top-pick {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(224,123,57,.08);
    }

    .top-pick-flag {
        position: absolute;
        top: -1px; right: 1rem;
        background: var(--accent);
        color: #fff;
        font-family: 'Syne', sans-serif;
        font-size: .65rem;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        padding: .2rem .6rem;
        border-radius: 0 0 6px 6px;
    }

    .provider-avatar {
        width: 48px; height: 48px;
        border-radius: 12px;
        background: var(--sidebar-bg);
        display: flex; align-items: center; justify-content: center;
        font-family: 'Syne', sans-serif;
        font-size: 1rem;
        font-weight: 700;
        color: #fff;
        flex-shrink: 0;
    }

    .provider-name {
        font-family: 'Syne', sans-serif;
        font-size: .95rem;
        font-weight: 600;
        color: var(--ink);
        margin-bottom: .25rem;
    }

    .provider-tags {
        display: flex;
        flex-wrap: wrap;
        gap: .4rem;
        margin-bottom: .4rem;
    }

    .tag {
        display: inline-flex;
        align-items: center;
        gap: .3rem;
        padding: .15rem .55rem;
        background: var(--bg);
        border: 1px solid var(--border);
        border-radius: 99px;
        font-size: .72rem;
        color: var(--ink-2);
    }

    .tag svg { width: 11px; height: 11px; }

    .stars {
        display: flex;
        align-items: center;
        gap: 2px;
        font-size: .78rem;
        color: var(--amber);
    }

    .stars svg { width: 12px; height: 12px; fill: var(--amber); color: var(--amber); }

    /* ── AI score bar ────────────────────────────────────────────── */
    .score-col { text-align: center; min-width: 90px; }

    .score-num {
        font-family: 'Syne', sans-serif;
        font-size: 1.4rem;
        font-weight: 700;
        color: var(--ink);
        line-height: 1;
        margin-bottom: .25rem;
    }

    .score-bar-wrap {
        height: 4px;
        background: var(--border);
        border-radius: 99px;
        overflow: hidden;
        margin-bottom: .25rem;
    }

    .score-bar {
        height: 100%;
        border-radius: 99px;
        background: var(--accent);
        transition: width 1s ease;
    }

    .score-label { font-size: .68rem; color: var(--ink-3); font-family: 'Syne', sans-serif; letter-spacing: .04em; }

    .btn-select {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .55rem 1.1rem;
        border-radius: var(--radius-sm);
        font-family: 'Syne', sans-serif;
        font-size: .8rem;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: opacity .15s;
    }

    .btn-select svg { width: 14px; height: 14px; }

    .provider-card.top-pick .btn-select {
        background: var(--accent);
        color: #fff;
    }

    .provider-card:not(.top-pick) .btn-select {
        background: var(--bg);
        color: var(--ink);
        border: 1.5px solid var(--border);
    }

    .btn-select:hover { opacity: .85; }

    /* ── Right sidebar: request summary ─────────────────────────── */
    .summary-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    .summary-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid var(--border);
        font-family: 'Syne', sans-serif;
        font-size: .85rem;
        font-weight: 600;
        color: var(--ink);
    }

    .summary-body { padding: 1rem 1.25rem; }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: .5rem;
        padding: .45rem 0;
        border-bottom: 1px solid var(--border);
        font-size: .82rem;
    }

    .summary-row:last-child { border-bottom: none; }
    .summary-key { color: var(--ink-3); }
    .summary-val { color: var(--ink); font-weight: 500; text-align: right; max-width: 60%; }

    @media (max-width: 860px) {
        .result-grid { grid-template-columns: 1fr; }
        .result-meta { display: none; }
    }
</style>
@endpush

@section('content')

{{-- ── AI result header ─────────────────────────────────────────────── --}}
<div class="result-header">
    <div class="ai-pulse"><i data-feather="cpu"></i></div>
    <div class="result-header-text">
        <div class="result-title">AI Recommendation Ready</div>
        <div class="result-sub">
            Found {{ count($payload['top_providers'] ?? []) }} matching provider(s) for your request
        </div>
    </div>
    <div class="result-meta">
        @if(isset($payload['confidence']))
            <div class="confidence-pill">
                <span class="confidence-dot"></span>
                {{ round($payload['confidence'] * 100) }}% confidence
            </div>
        @endif
        <span style="font-size:.75rem; color:rgba(255,255,255,.3);">
            Request #{{ $serviceRequest->id }}
        </span>
    </div>
</div>

<div class="result-grid">
    <div>

        {{-- Recommended service --}}
        @if(isset($payload['recommended_service']))
        <div class="service-box">
            <div class="service-box-icon"><i data-feather="tool"></i></div>
            <div>
                <div class="service-box-label">Recommended service</div>
                <div class="service-box-name">{{ $payload['recommended_service'] }}</div>
            </div>
        </div>
        @endif

        {{-- Provider cards --}}
        <div class="providers-title">Select a provider to proceed</div>

        <form method="POST" action="{{ route('customer.accept-provider', $serviceRequest->id) }}" id="acceptForm">
            @csrf

            <div class="provider-cards">
                @forelse($payload['top_providers'] ?? [] as $index => $provider)
                    @php
                        $isTop    = $index === 0;
                        $initials = strtoupper(substr($provider['name'], 0, 2));
                        $score    = round(($provider['score'] ?? 0) * 100);
                        $rating   = $provider['avg_rating'] ?? 0;
                        $jobs     = $provider['total_jobs'] ?? 0;
                        $rate     = $provider['hourly_rate'] ?? null;
                        $exp      = $provider['years_experience'] ?? 0;
                    @endphp

                    <div class="provider-card {{ $isTop ? 'top-pick' : '' }}">
                        @if($isTop)
                            <span class="top-pick-flag">Best Match</span>
                        @endif

                        <div class="provider-avatar">{{ $initials }}</div>

                        <div>
                            <div class="provider-name">{{ $provider['name'] }}</div>
                            <div class="provider-tags">
                                <span class="tag">
                                    <i data-feather="briefcase"></i>
                                    {{ $exp }} yr{{ $exp !== 1 ? 's' : '' }} exp
                                </span>
                                <span class="tag">
                                    <i data-feather="check-square"></i>
                                    {{ $jobs }} jobs
                                </span>
                                @if($rate)
                                    <span class="tag">
                                        <i data-feather="dollar-sign"></i>
                                        ₹{{ number_format($rate) }}/hr
                                    </span>
                                @endif
                            </div>
                            <div class="stars">
                                @for($s = 1; $s <= 5; $s++)
                                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"
                                         style="{{ $s <= round($rating) ? '' : 'fill:none; color:var(--border);' }}">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                    </svg>
                                @endfor
                                <span style="margin-left:.3rem; color:var(--ink-2);">{{ number_format($rating, 1) }}</span>
                            </div>
                        </div>

                        <div style="display:flex; flex-direction:column; align-items:flex-end; gap:.75rem;">
                            <div class="score-col">
                                <div class="score-num">{{ $score }}%</div>
                                <div class="score-bar-wrap">
                                    <div class="score-bar" style="width:{{ $score }}%"></div>
                                </div>
                                <div class="score-label">AI MATCH</div>
                            </div>
                            <button type="submit" name="provider_id" value="{{ $provider['provider_id'] }}"
                                    class="btn-select"
                                    onclick="confirmSelect(event, '{{ $provider['name'] }}')">
                                <i data-feather="check"></i>
                                Select
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="card" style="padding:2.5rem; text-align:center; color:var(--ink-3);">
                        <i data-feather="alert-circle" style="width:32px;height:32px;margin-bottom:.5rem;"></i>
                        <p>No providers found for your criteria. Our team will reach out manually.</p>
                    </div>
                @endforelse
            </div>
        </form>

        <div style="margin-top:1rem; font-size:.8rem; color:var(--ink-3); display:flex; align-items:center; gap:.4rem;">
            <i data-feather="info" style="width:13px;height:13px;"></i>
            Selecting a provider sends them a job notification. They will confirm within 30 minutes.
        </div>

    </div>

    {{-- ── Request summary sidebar ─────────────────────────────── --}}
    <div>
        <div class="summary-card">
            <div class="summary-header">Your Request Summary</div>
            <div class="summary-body">
                <div class="summary-row">
                    <span class="summary-key">Title</span>
                    <span class="summary-val">{{ $serviceRequest->title }}</span>
                </div>
                @if($serviceRequest->service)
                <div class="summary-row">
                    <span class="summary-key">Service</span>
                    <span class="summary-val">{{ $serviceRequest->service->name }}</span>
                </div>
                @endif
                <div class="summary-row">
                    <span class="summary-key">Location</span>
                    <span class="summary-val">{{ $serviceRequest->city }}, {{ $serviceRequest->pincode }}</span>
                </div>
                @if($serviceRequest->budget_max)
                <div class="summary-row">
                    <span class="summary-key">Budget</span>
                    <span class="summary-val">
                        ₹{{ number_format($serviceRequest->budget_min ?? 0) }} –
                        ₹{{ number_format($serviceRequest->budget_max) }}
                    </span>
                </div>
                @endif
                @if($serviceRequest->preferred_date)
                <div class="summary-row">
                    <span class="summary-key">Preferred date</span>
                    <span class="summary-val">{{ $serviceRequest->preferred_date->format('d M Y') }}</span>
                </div>
                @endif
                <div class="summary-row">
                    <span class="summary-key">Submitted</span>
                    <span class="summary-val">{{ $serviceRequest->created_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>

        <div style="margin-top:.75rem; padding:1rem 1.1rem; background:var(--surface); border:1px solid var(--border); border-radius:var(--radius); font-size:.8rem; color:var(--ink-2); line-height:1.7;">
            <strong style="font-family:'Syne',sans-serif; display:block; margin-bottom:.3rem;">Not satisfied?</strong>
            You can <a href="{{ route('customer.requests') }}" style="color:var(--accent);">view all your requests</a>
            and wait for admin to assign manually if none of these providers feel right.
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function confirmSelect(e, name) {
    if (!confirm(`Confirm selection of "${name}" for this job?`)) {
        e.preventDefault();
    }
}
</script>
@endpush
