@extends('layouts.provider')

@section('title', 'New Job Offers')

@push('styles')
<style>
    .page-header { margin-bottom: 1.5rem; }
    .page-title  { font-family:'Barlow Condensed',sans-serif; font-size:1.7rem; font-weight:700; letter-spacing:.02em; color:var(--text-1); }
    .page-sub    { font-size:.875rem; color:var(--text-2); margin-top:.3rem; }

    /* ── Job offer card ──────────────────────────────────────────── */
    .job-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        margin-bottom: 1rem;
        overflow: hidden;
        transition: border-color .15s;
    }

    .job-card:hover { border-color: var(--border-2); }

    .job-card.fresh { border-left: 3px solid var(--teal); }

    .job-top {
        padding: 1.25rem;
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 1rem;
        align-items: start;
    }

    .job-title {
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 1.2rem;
        font-weight: 700;
        letter-spacing: .02em;
        color: var(--text-1);
        margin-bottom: .4rem;
    }

    .job-chips { display:flex; flex-wrap:wrap; gap:.4rem; margin-bottom:.75rem; }

    .chip {
        display: inline-flex; align-items: center; gap: .3rem;
        padding: .25rem .65rem;
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: 99px;
        font-size: .78rem;
        color: var(--text-2);
    }

    .chip svg { width: 11px; height: 11px; }
    .chip.highlight { background:var(--teal-dim); border-color:rgba(0,201,167,.2); color:var(--teal); }

    .job-desc {
        font-size: .875rem;
        color: var(--text-2);
        line-height: 1.6;
    }

    /* ── AI score chip (top right) ───────────────────────────────── */
    .ai-score-block {
        display: flex;
        flex-direction: column;
        align-items: center;
        background: var(--surface-2);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: .75rem 1rem;
        min-width: 90px;
        flex-shrink: 0;
    }

    .ai-score-num {
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 2rem;
        font-weight: 700;
        color: var(--teal);
        line-height: 1;
    }

    .ai-score-bar {
        height: 3px;
        width: 100%;
        background: var(--surface-3);
        border-radius: 99px;
        margin: .35rem 0 .25rem;
        overflow: hidden;
    }

    .ai-score-fill { height: 100%; background: var(--teal); border-radius: 99px; }
    .ai-score-label { font-size: .62rem; letter-spacing: .1em; text-transform: uppercase; color: var(--text-3); }

    /* ── Bottom action bar ───────────────────────────────────────── */
    .job-bottom {
        padding: .85rem 1.25rem;
        background: var(--surface-2);
        border-top: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: .75rem;
        flex-wrap: wrap;
    }

    /* Inline quote input */
    .quote-form {
        display: flex;
        align-items: center;
        gap: .5rem;
        margin-left: auto;
    }

    .quote-input-wrap {
        position: relative;
    }

    .quote-input-prefix {
        position: absolute; left: .65rem; top:50%; transform:translateY(-50%);
        font-size: .85rem; color: var(--text-2); pointer-events: none;
    }

    .quote-input {
        width: 130px;
        padding: .5rem .75rem .5rem 1.6rem;
        background: var(--surface);
        border: 1px solid var(--border-2);
        border-radius: var(--radius-sm);
        font-family: 'Barlow', sans-serif;
        font-size: .875rem;
        color: var(--text-1);
        outline: none;
        transition: border-color .15s;
    }

    .quote-input::placeholder { color: var(--text-3); }
    .quote-input:focus { border-color: var(--teal); }

    .job-received { font-size: .78rem; color: var(--text-3); }
    .job-received svg { width: 12px; height: 12px; display: inline; vertical-align: middle; }

    /* ── Empty / pagination ──────────────────────────────────────── */
    .pagination-row {
        display: flex; align-items: center; justify-content: space-between;
        padding: 1rem 0; font-size: .82rem; color: var(--text-2);
    }

    .pg-links { display: flex; gap: .3rem; }

    .pg-btn {
        width: 32px; height: 32px;
        display:flex; align-items:center; justify-content:center;
        border-radius: 6px; text-decoration:none;
        font-family:'Barlow Condensed',sans-serif; font-size:.85rem; font-weight:600;
        color: var(--text-2);
        border: 1px solid var(--border-2);
        transition: border-color .12s, color .12s;
    }

    .pg-btn:hover { border-color: var(--teal); color: var(--teal); }
    .pg-btn.cur   { background: var(--teal); color: #000; border-color: var(--teal); }
</style>
@endpush

@section('content')

<div class="page-header">
    <h1 class="page-title">New Job Offers</h1>
    <p class="page-sub">
        {{ $jobs->total() }} offer{{ $jobs->total() !== 1 ? 's' : '' }} waiting for your response
    </p>
</div>

@if($jobs->isEmpty())
    <div class="card">
        <div class="empty-state" style="padding:4rem 1rem;">
            <i data-feather="inbox"></i>
            <div class="empty-title">No offers right now</div>
            <p class="empty-sub">When a customer selects you from AI recommendations, the job offer appears here.</p>
        </div>
    </div>
@else
    @foreach($jobs as $job)
        @php
            $req     = $job->serviceRequest;
            $isFresh = $job->created_at->gt(now()->subHours(3));

            // Extract AI match score for this provider if available
            $aiScore = null;
            $payload = $req->ai_recommendation_payload;
            if ($payload) {
                foreach (($payload['top_providers'] ?? []) as $p) {
                    if ($p['provider_id'] === auth()->id()) {
                        $aiScore = round($p['score'] * 100);
                        break;
                    }
                }
            }
        @endphp

        <div class="job-card {{ $isFresh ? 'fresh' : '' }}">
            <div class="job-top">
                <div>
                    <div class="job-title">{{ $req->title }}</div>

                    <div class="job-chips">
                        @if($req->service)
                            <span class="chip highlight">{{ $req->service->name }}</span>
                        @endif
                        @if($req->service?->category)
                            <span class="chip">{{ $req->service->category->name }}</span>
                        @endif
                        <span class="chip"><i data-feather="user"></i> {{ $req->customer->name }}</span>
                        <span class="chip"><i data-feather="map-pin"></i> {{ $req->city }}, {{ $req->pincode }}</span>
                        @if($req->budget_max)
                            <span class="chip"><i data-feather="dollar-sign"></i> ₹{{ number_format($req->budget_min ?? 0) }} – ₹{{ number_format($req->budget_max) }}</span>
                        @endif
                        @if($req->preferred_date)
                            <span class="chip"><i data-feather="calendar"></i> {{ $req->preferred_date->format('d M Y') }}</span>
                        @endif
                        @if($req->preferred_time)
                            <span class="chip"><i data-feather="clock"></i> {{ \Carbon\Carbon::parse($req->preferred_time)->format('h:i A') }}</span>
                        @endif
                    </div>

                    <p class="job-desc">{{ $req->description }}</p>
                </div>

                {{-- AI score (only shown if we have it) --}}
                @if($aiScore)
                    <div class="ai-score-block">
                        <div class="ai-score-num">{{ $aiScore }}%</div>
                        <div class="ai-score-bar">
                            <div class="ai-score-fill" style="width:{{ $aiScore }}%;"></div>
                        </div>
                        <div class="ai-score-label">AI Match</div>
                    </div>
                @endif
            </div>

            {{-- Action bar --}}
            <div class="job-bottom">
                {{-- Accept --}}
                <form method="POST" action="{{ route('provider.jobs.accept', $job->id) }}">
                    @csrf
                    <button type="submit" class="btn btn-teal">
                        <i data-feather="check-circle"></i> Accept Job
                    </button>
                </form>

                {{-- Decline --}}
                <form method="POST" action="{{ route('provider.jobs.reject', $job->id) }}"
                      onsubmit="return confirm('Decline this offer?')">
                    @csrf
                    <input type="hidden" name="reason" value="Provider unavailable">
                    <button type="submit" class="btn btn-red">
                        <i data-feather="x"></i> Decline
                    </button>
                </form>

                <span class="job-received">
                    <i data-feather="clock"></i>
                    Received {{ $job->created_at->diffForHumans() }}
                </span>

                {{-- Optional: quote a price before accepting --}}
                <form method="POST" action="{{ route('provider.jobs.price', $job->id) }}" class="quote-form">
                    @csrf
                    <div class="quote-input-wrap">
                        <span class="quote-input-prefix">₹</span>
                        <input type="number" name="quoted_price" class="quote-input"
                               placeholder="Quote price"
                               value="{{ $job->quoted_price ? (int)$job->quoted_price : '' }}"
                               min="0" step="50">
                    </div>
                    <button type="submit" class="btn btn-ghost" style="padding:.5rem .85rem;">
                        <i data-feather="save"></i> Save
                    </button>
                </form>
            </div>
        </div>
    @endforeach

    {{-- Pagination --}}
    @if($jobs->hasPages())
        <div class="pagination-row">
            <span>Showing {{ $jobs->firstItem() }}–{{ $jobs->lastItem() }} of {{ $jobs->total() }}</span>
            <div class="pg-links">
                @if(!$jobs->onFirstPage())
                    <a href="{{ $jobs->previousPageUrl() }}" class="pg-btn">
                        <i data-feather="chevron-left" style="width:14px;height:14px;"></i>
                    </a>
                @endif
                @foreach($jobs->getUrlRange(1, $jobs->lastPage()) as $pg => $url)
                    <a href="{{ $url }}" class="pg-btn {{ $pg === $jobs->currentPage() ? 'cur' : '' }}">{{ $pg }}</a>
                @endforeach
                @if($jobs->hasMorePages())
                    <a href="{{ $jobs->nextPageUrl() }}" class="pg-btn">
                        <i data-feather="chevron-right" style="width:14px;height:14px;"></i>
                    </a>
                @endif
            </div>
        </div>
    @endif
@endif

@endsection
