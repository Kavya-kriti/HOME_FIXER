@extends('layouts.provider')

@section('title', 'My Jobs')

@push('styles')
<style>
    .page-header { margin-bottom: 1.5rem; display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; }
    .page-title  { font-family:'Barlow Condensed',sans-serif; font-size:1.7rem; font-weight:700; letter-spacing:.02em; color:var(--text-1); }
    .page-sub    { font-size:.875rem; color:var(--text-2); margin-top:.25rem; }

    /* ── Tab bar ─────────────────────────────────────────────────── */
    .tab-row {
        display: flex; gap: .25rem;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: .3rem;
        margin-bottom: 1.5rem;
        width: fit-content;
        flex-wrap: wrap;
    }

    .tab {
        padding: .4rem .9rem;
        border-radius: 6px;
        font-family: 'Barlow Condensed', sans-serif;
        font-size: .85rem;
        font-weight: 600;
        letter-spacing: .03em;
        color: var(--text-2);
        text-decoration: none;
        transition: background .12s, color .12s;
        display: flex; align-items: center; gap: .4rem;
    }

    .tab .tc {
        background: var(--surface-2);
        color: var(--text-3);
        font-size: .68rem;
        padding: .1rem .4rem;
        border-radius: 99px;
    }

    .tab.active { background: var(--teal-dim); color: var(--teal); }
    .tab.active .tc { background: rgba(0,201,167,.15); color: var(--teal); }
    .tab:not(.active):hover { background: var(--surface-2); color: var(--text-1); }

    /* ── Job row card ────────────────────────────────────────────── */
    .job-row {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        margin-bottom: .75rem;
        overflow: hidden;
        transition: border-color .15s;
    }

    .job-row:hover { border-color: var(--border-2); }

    /* Active jobs get a colored left accent */
    .job-row.status-started  { border-left: 3px solid var(--orange); }
    .job-row.status-accepted { border-left: 3px solid var(--blue); }
    .job-row.status-done     { border-left: 3px solid var(--teal); }

    .job-row-top {
        padding: 1.1rem 1.25rem;
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 1rem;
        align-items: center;
    }

    .job-row-title {
        font-family: 'Barlow Condensed', sans-serif;
        font-size: 1.1rem;
        font-weight: 700;
        letter-spacing: .02em;
        color: var(--text-1);
        margin-bottom: .3rem;
    }

    .job-row-meta {
        display: flex; flex-wrap: wrap; gap: .6rem;
        font-size: .8rem; color: var(--text-2);
        align-items: center;
    }

    .job-row-meta-item { display:flex; align-items:center; gap:.25rem; }
    .job-row-meta-item svg { width:12px; height:12px; }

    /* ── Timeline strip ──────────────────────────────────────────── */
    .timeline-strip {
        padding: .75rem 1.25rem;
        background: var(--surface-2);
        border-top: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: 0;
    }

    .tl-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: .2rem;
        flex: 1;
        position: relative;
    }

    .tl-step::after {
        content: '';
        position: absolute;
        left: 50%; right: -50%;
        top: 10px;
        height: 2px;
        background: var(--border-2);
        z-index: 0;
    }

    .tl-step:last-child::after { display: none; }

    .tl-dot {
        width: 20px; height: 20px;
        border-radius: 50%;
        border: 2px solid var(--border-2);
        background: var(--surface);
        display: flex; align-items: center; justify-content: center;
        position: relative;
        z-index: 1;
    }

    .tl-dot.done  { background: var(--teal); border-color: var(--teal); }
    .tl-dot.done svg { width: 10px; height: 10px; color: #000; }
    .tl-dot.active { border-color: var(--amber); }
    .tl-dot.active::before { content:''; width:8px; height:8px; border-radius:50%; background:var(--amber); }

    .tl-label {
        font-family: 'Barlow Condensed', sans-serif;
        font-size: .65rem;
        letter-spacing: .06em;
        text-transform: uppercase;
        color: var(--text-3);
        text-align: center;
    }

    .tl-label.done   { color: var(--teal); }
    .tl-label.active { color: var(--amber); }

    .tl-step.done::after { background: var(--teal); }

    /* ── Action bar ──────────────────────────────────────────────── */
    .job-action-bar {
        padding: .85rem 1.25rem;
        border-top: 1px solid var(--border);
        display: flex;
        align-items: center;
        gap: .75rem;
        flex-wrap: wrap;
    }

    /* Complete job modal trigger */
    .complete-form-panel {
        display: none;
        padding: 1rem 1.25rem;
        background: var(--surface-2);
        border-top: 1px solid var(--border);
    }

    .complete-form-panel.open { display: block; }

    .complete-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: .85rem; }

    input[type="number"].dark-input,
    textarea.dark-input {
        width: 100%;
        padding: .6rem .85rem;
        background: var(--surface);
        border: 1px solid var(--border-2);
        border-radius: var(--radius-sm);
        font-family: 'Barlow', sans-serif;
        font-size: .875rem;
        color: var(--text-1);
        outline: none;
        transition: border-color .15s;
    }

    textarea.dark-input { resize: vertical; min-height: 70px; padding: .6rem .85rem; }
    input[type="number"].dark-input:focus,
    textarea.dark-input:focus { border-color: var(--teal); }

    .dark-label {
        display: block;
        font-size: .72rem;
        font-weight: 600;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--text-2);
        margin-bottom: .35rem;
    }

    /* ── Pagination ──────────────────────────────────────────────── */
    .pagination-row {
        display:flex; align-items:center; justify-content:space-between;
        padding: 1rem 0; font-size:.82rem; color:var(--text-2);
    }

    .pg-links { display:flex; gap:.3rem; }

    .pg-btn {
        width:32px; height:32px;
        display:flex; align-items:center; justify-content:center;
        border-radius:6px; text-decoration:none;
        font-family:'Barlow Condensed',sans-serif; font-size:.85rem; font-weight:600;
        color:var(--text-2); border:1px solid var(--border-2);
        transition: border-color .12s, color .12s;
    }

    .pg-btn:hover { border-color:var(--teal); color:var(--teal); }
    .pg-btn.cur   { background:var(--teal); color:#000; border-color:var(--teal); }
</style>
@endpush

@section('content')

<div class="page-header">
    <div>
        <h1 class="page-title">My Jobs</h1>
        <p class="page-sub">{{ $jobs->total() }} job{{ $jobs->total() !== 1 ? 's' : '' }} in your history</p>
    </div>
</div>

{{-- Status tabs --}}
@php
    $tabs = ['all'=>'All', 'accepted'=>'Accepted', 'started'=>'In Progress', 'done'=>'Completed', 'rejected'=>'Declined'];
    $cur  = request('status', 'all');
@endphp
<div class="tab-row">
    @foreach($tabs as $k => $label)
        <a href="{{ route('provider.my-jobs', $k !== 'all' ? ['status' => $k] : []) }}"
           class="tab {{ $cur === $k ? 'active' : '' }}">
            {{ $label }}
            <span class="tc">{{ $k === 'all' ? $jobs->total() : ($counts[$k] ?? 0) }}</span>
        </a>
    @endforeach
</div>

@if($jobs->isEmpty())
    <div class="card">
        <div class="empty-state" style="padding:4rem 1rem;">
            <i data-feather="briefcase"></i>
            <div class="empty-title">No jobs yet</div>
            <p class="empty-sub">
                @if($cur !== 'all')
                    No {{ str_replace('_',' ',$cur) }} jobs.
                    <a href="{{ route('provider.my-jobs') }}" style="color:var(--teal);">View all</a>
                @else
                    Accept job offers to see them appear here.
                @endif
            </p>
        </div>
    </div>
@else
    @foreach($jobs as $job)
        @php
            $req   = $job->serviceRequest;
            $badge = $job->status_badge;

            // Timeline state
            $tl = [
                'offered'  => in_array($job->status, ['offered','accepted','started','done']),
                'accepted' => in_array($job->status, ['accepted','started','done']),
                'started'  => in_array($job->status, ['started','done']),
                'done'     => $job->status === 'done',
            ];
        @endphp

        <div class="job-row status-{{ $job->status }}" id="job-{{ $job->id }}">

            {{-- Top row --}}
            <div class="job-row-top">
                <div>
                    <div class="job-row-title">{{ $req->title }}</div>
                    <div class="job-row-meta">
                        @if($req->service)
                            <span class="badge badge-teal">{{ $req->service->name }}</span>
                        @endif
                        <span class="job-row-meta-item"><i data-feather="user"></i> {{ $req->customer->name }}</span>
                        <span class="job-row-meta-item"><i data-feather="map-pin"></i> {{ $req->city }}</span>
                        @if($job->quoted_price)
                            <span class="job-row-meta-item"><i data-feather="dollar-sign"></i> ₹{{ number_format($job->quoted_price) }}</span>
                        @endif
                        <span class="job-row-meta-item"><i data-feather="clock"></i> {{ $job->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
                <span class="badge badge-{{ $badge['color'] }}">{{ $badge['label'] }}</span>
            </div>

            {{-- Progress timeline (only for non-rejected) --}}
            @if($job->status !== 'rejected')
                <div class="timeline-strip">
                    @php
                        $steps = [
                            'offered'  => ['label' => 'Offered',  'time' => $job->assigned_at],
                            'accepted' => ['label' => 'Accepted', 'time' => $job->accepted_at],
                            'started'  => ['label' => 'Started',  'time' => $job->started_at],
                            'done'     => ['label' => 'Completed','time' => $job->completed_at],
                        ];
                        $stepKeys = array_keys($steps);
                        $currentIdx = array_search($job->status, $stepKeys);
                    @endphp

                    @foreach($steps as $key => $step)
                        @php
                            $idx       = array_search($key, $stepKeys);
                            $isDone    = $tl[$key];
                            $isActive  = $key === $job->status && $job->status !== 'done';
                            $stateClass = $isDone ? 'done' : ($isActive ? 'active' : '');
                        @endphp
                        <div class="tl-step {{ $isDone ? 'done' : '' }}">
                            <div class="tl-dot {{ $stateClass }}">
                                @if($isDone)
                                    <i data-feather="check"></i>
                                @endif
                            </div>
                            <div class="tl-label {{ $stateClass }}">
                                {{ $step['label'] }}
                                @if($step['time'])
                                    <br><span style="font-size:.6rem; opacity:.7;">{{ $step['time']->format('d M, H:i') }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Action bar --}}
            @if(in_array($job->status, ['accepted', 'started']))
                <div class="job-action-bar">

                    @if($job->status === 'accepted')
                        <form method="POST" action="{{ route('provider.jobs.start', $job->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-teal">
                                <i data-feather="play"></i> Mark as Started
                            </button>
                        </form>
                    @endif

                    @if($job->status === 'started')
                        <button type="button" class="btn btn-teal"
                                onclick="toggleCompletePanel({{ $job->id }})">
                            <i data-feather="check-square"></i> Mark as Complete
                        </button>
                    @endif

                    <a href="tel:{{ $req->customer->phone }}" class="btn btn-ghost">
                        <i data-feather="phone"></i>
                        Call {{ explode(' ', $req->customer->name)[0] }}
                    </a>

                    <span style="font-size:.8rem; color:var(--text-3); margin-left:auto;">
                        {{ $req->address }}
                    </span>
                </div>

                {{-- Complete job form (hidden until button clicked) --}}
                @if($job->status === 'started')
                    <div class="complete-form-panel" id="complete-panel-{{ $job->id }}">
                        <form method="POST" action="{{ route('provider.jobs.complete', $job->id) }}">
                            @csrf
                            <div class="complete-grid">
                                <div>
                                    <label class="dark-label">Final price charged (₹)</label>
                                    <input type="number" name="quoted_price" class="dark-input"
                                           placeholder="e.g. 850"
                                           value="{{ $job->quoted_price ? (int)$job->quoted_price : '' }}"
                                           min="0" step="50">
                                </div>
                                <div>
                                    <label class="dark-label">Job notes <span style="font-weight:400; text-transform:none; letter-spacing:0;">(optional)</span></label>
                                    <textarea name="provider_notes" class="dark-input"
                                              placeholder="Parts used, special instructions for customer...">{{ $job->provider_notes }}</textarea>
                                </div>
                            </div>
                            <div style="display:flex; gap:.5rem;">
                                <button type="submit" class="btn btn-teal">
                                    <i data-feather="check-circle"></i> Confirm Completion
                                </button>
                                <button type="button" class="btn btn-ghost"
                                        onclick="toggleCompletePanel({{ $job->id }})">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                @endif
            @endif

            {{-- Review snippet if completed and reviewed --}}
            @if($job->status === 'done')
                @php $review = $req->reviews->where('reviewee_id', auth()->id())->first(); @endphp
                @if($review)
                    <div style="padding:.75rem 1.25rem; border-top:1px solid var(--border); display:flex; align-items:center; gap:.75rem; font-size:.82rem; color:var(--text-2);">
                        <div style="display:flex; gap:2px;">
                            @for($s=1;$s<=5;$s++)
                                <svg width="13" height="13" viewBox="0 0 24 24"
                                     style="{{ $s<=$review->rating ? 'fill:var(--amber);color:var(--amber)' : 'fill:none;color:var(--border-2)' }}">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                </svg>
                            @endfor
                        </div>
                        <span>"{{ $review->comment }}"</span>
                        <span style="color:var(--text-3); margin-left:auto;">— {{ $req->customer->name }}</span>
                    </div>
                @else
                    <div style="padding:.65rem 1.25rem; border-top:1px solid var(--border); font-size:.78rem; color:var(--text-3);">
                        Awaiting customer review
                    </div>
                @endif
            @endif
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

@push('scripts')
<script>
function toggleCompletePanel(jobId) {
    const panel = document.getElementById('complete-panel-' + jobId);
    panel.classList.toggle('open');
    if (panel.classList.contains('open')) {
        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
}
</script>
@endpush
