@extends('layouts.admin')
@section('title','AI Logs')
@section('page-title','AI Recommendation Logs')

@push('styles')
<style>
    .log-summary {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: .75rem;
        margin-bottom: 1.1rem;
    }

    .log-kpi {
        background: var(--white);
        border: 1px solid var(--slate-200);
        border-radius: var(--radius);
        padding: .85rem 1rem;
        box-shadow: var(--shadow-sm);
    }

    .log-kpi-val {
        font-family: 'Instrument Serif', serif;
        font-size: 1.6rem;
        color: var(--slate-900);
        line-height: 1;
        margin-bottom: .2rem;
    }

    .log-kpi-label { font-size: .7rem; color: var(--slate-400); letter-spacing: .05em; }

    /* ── Log row ─────────────────────────────────────────────────── */
    .log-status-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
    }

    .payload-toggle {
        background: none; border: none; cursor: pointer;
        color: var(--indigo); font-family: 'Instrument Sans', sans-serif;
        font-size: .75rem; font-weight: 500; padding: 0;
        text-decoration: underline; text-underline-offset: 2px;
    }

    /* JSON payload drawer */
    .payload-drawer {
        display: none;
        padding: .75rem 1.1rem;
        background: var(--slate-950);
        border-top: 1px solid var(--slate-200);
    }

    .payload-drawer.open { display: block; }

    .payload-cols {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
    }

    .payload-title {
        font-size: .65rem;
        font-weight: 600;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--slate-500);
        margin-bottom: .4rem;
        font-family: 'Instrument Sans', sans-serif;
    }

    pre.payload-json {
        font-size: .72rem;
        color: #A5B4FC;
        font-family: 'Courier New', monospace;
        white-space: pre-wrap;
        word-break: break-all;
        line-height: 1.6;
        max-height: 220px;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: var(--slate-700) transparent;
    }

    /* Response time bar */
    .rt-bar-wrap {
        width: 60px; height: 4px;
        background: var(--slate-200);
        border-radius: 99px;
        overflow: hidden;
        display: inline-block;
        vertical-align: middle;
        margin-left: .35rem;
    }

    .rt-bar { height: 100%; border-radius: 99px; }

    @media (max-width: 768px) {
        .log-summary { grid-template-columns: repeat(2,1fr); }
        .payload-cols { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

@php
    $totalLogs   = \App\Models\AiRecommendationLog::count();
    $successLogs = \App\Models\AiRecommendationLog::where('success', true)->count();
    $failLogs    = $totalLogs - $successLogs;
    $avgMs       = (int) \App\Models\AiRecommendationLog::where('success', true)->avg('response_time_ms');
    $maxMs       = (int) \App\Models\AiRecommendationLog::max('response_time_ms');
    $successRate = $totalLogs > 0 ? round(($successLogs / $totalLogs) * 100, 1) : 0;
@endphp

{{-- Summary KPIs --}}
<div class="log-summary">
    <div class="log-kpi">
        <div class="log-kpi-val">{{ number_format($totalLogs) }}</div>
        <div class="log-kpi-label">Total AI calls</div>
    </div>
    <div class="log-kpi">
        <div class="log-kpi-val" style="{{ $successRate >= 90 ? 'color:var(--green)' : 'color:var(--amber)' }}">
            {{ $successRate }}%
        </div>
        <div class="log-kpi-label">Success rate</div>
    </div>
    <div class="log-kpi">
        <div class="log-kpi-val">{{ $avgMs }}<span style="font-size:1rem; font-family:'Instrument Sans',sans-serif;">ms</span></div>
        <div class="log-kpi-label">Avg response time</div>
    </div>
    <div class="log-kpi">
        <div class="log-kpi-val" style="{{ $failLogs > 0 ? 'color:var(--red)' : '' }}">{{ $failLogs }}</div>
        <div class="log-kpi-label">Failed calls</div>
    </div>
</div>

<div class="card">
    <div class="card-head">
        <span class="card-title">Recommendation audit log</span>
        <span style="font-size:.78rem; color:var(--slate-400);">
            Most recent first · {{ $logs->total() }} total entries
        </span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:30px;"></th>
                <th>ID</th>
                <th>Request</th>
                <th>Customer</th>
                <th>Status</th>
                <th>Confidence</th>
                <th>Providers</th>
                <th>Response time</th>
                <th>Model</th>
                <th>Called at</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                @php
                    $out       = $log->output_payload ?? [];
                    $conf      = isset($out['confidence']) ? round($out['confidence'] * 100) : null;
                    $providers = $out['top_providers'] ?? [];
                    $rtMs      = $log->response_time_ms ?? 0;
                    $rtPct     = $maxMs > 0 ? min(round(($rtMs / $maxMs) * 100), 100) : 0;
                    $rtColor   = $rtMs > 1000 ? 'var(--red)' : ($rtMs > 500 ? 'var(--amber)' : 'var(--green)');
                @endphp

                <tr>
                    <td>
                        <button type="button" class="payload-toggle"
                                onclick="togglePayload({{ $log->id }})">
                            JSON
                        </button>
                    </td>
                    <td style="color:var(--slate-400); font-size:.75rem;">{{ $log->id }}</td>
                    <td style="font-size:.8rem;">
                        @if($log->serviceRequest)
                            <strong>#{{ $log->request_id }}</strong>
                            <div style="font-size:.72rem; color:var(--slate-400); max-width:160px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                {{ $log->serviceRequest->title }}
                            </div>
                        @else
                            <span style="color:var(--slate-300);">Deleted</span>
                        @endif
                    </td>
                    <td style="font-size:.8rem;">
                        {{ $log->serviceRequest?->customer?->name ?? '—' }}
                    </td>
                    <td>
                        <div style="display:flex; align-items:center; gap:.45rem;">
                            <span class="log-status-dot"
                                  style="background:{{ $log->success ? 'var(--green)' : 'var(--red)' }};
                                         {{ $log->success ? 'box-shadow:0 0 5px var(--green);' : '' }}">
                            </span>
                            <span class="badge {{ $log->success ? 'b-green' : 'b-red' }}">
                                {{ $log->success ? 'Success' : 'Failed' }}
                            </span>
                        </div>
                        @if(!$log->success && $log->error_message)
                            <div style="font-size:.68rem; color:var(--red); margin-top:.2rem; max-width:140px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;"
                                 title="{{ $log->error_message }}">
                                {{ Str::limit($log->error_message, 30) }}
                            </div>
                        @endif
                    </td>
                    <td>
                        @if($conf !== null)
                            <div style="display:flex; align-items:center; gap:.4rem;">
                                <span style="font-family:'Instrument Serif',serif; font-size:.95rem;">{{ $conf }}%</span>
                                <div style="width:36px; height:3px; background:var(--slate-200); border-radius:99px; overflow:hidden;">
                                    <div style="height:100%; width:{{ $conf }}%; background:{{ $conf>=80?'var(--green)':($conf>=60?'var(--amber)':'var(--red)') }}; border-radius:99px;"></div>
                                </div>
                            </div>
                        @else
                            <span style="color:var(--slate-300);">—</span>
                        @endif
                    </td>
                    <td style="font-size:.8rem;">
                        @if(count($providers) > 0)
                            @foreach(array_slice($providers, 0, 2) as $p)
                                <div style="white-space:nowrap; margin-bottom:.1rem;">
                                    {{ $p['name'] }}
                                    <span style="color:var(--slate-400); font-size:.7rem;">({{ round($p['score']*100) }}%)</span>
                                </div>
                            @endforeach
                            @if(count($providers) > 2)
                                <div style="font-size:.7rem; color:var(--slate-400);">+{{ count($providers)-2 }} more</div>
                            @endif
                        @else
                            <span style="color:var(--slate-300);">None</span>
                        @endif
                    </td>
                    <td>
                        @if($rtMs)
                            <span style="font-size:.8rem; color:{{ $rtColor }}; font-weight:500;">{{ $rtMs }}ms</span>
                            <div class="rt-bar-wrap">
                                <div class="rt-bar" style="width:{{ $rtPct }}%; background:{{ $rtColor }};"></div>
                            </div>
                        @else
                            <span style="color:var(--slate-300); font-size:.8rem;">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge b-slate">{{ $log->model_version }}</span>
                    </td>
                    <td style="font-size:.75rem; color:var(--slate-400); white-space:nowrap;">
                        {{ $log->created_at->format('d M, H:i') }}
                    </td>
                </tr>

                {{-- JSON drawer --}}
                <tr>
                    <td colspan="10" style="padding:0; border:none;">
                        <div class="payload-drawer" id="payload-{{ $log->id }}">
                            <div class="payload-cols">
                                <div>
                                    <div class="payload-title">Input payload (sent to Python)</div>
                                    <pre class="payload-json">{{ json_encode($log->input_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                                <div>
                                    <div class="payload-title">Output payload (returned from Python)</div>
                                    <pre class="payload-json">{{ json_encode($log->output_payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                </div>
                            </div>
                            @if($log->error_message)
                                <div style="margin-top:.6rem; font-size:.75rem; color:#F87171; font-family:monospace;">
                                    Error: {{ $log->error_message }}
                                </div>
                            @endif
                        </div>
                    </td>
                </tr>

            @empty
                <tr><td colspan="10">
                    <div class="empty">
                        <i data-feather="cpu"></i>
                        <div class="empty-t">No AI logs yet</div>
                        <div class="empty-s">Logs appear here after customers submit service requests.</div>
                    </div>
                </td></tr>
            @endforelse
        </tbody>
    </table>

    @if($logs->hasPages())
        <div class="pag-row">
            <span>Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }}</span>
            <div class="pag-links">
                @if(!$logs->onFirstPage())
                    <a href="{{ $logs->previousPageUrl() }}" class="pg">
                        <i data-feather="chevron-left" style="width:12px;height:12px;"></i>
                    </a>
                @endif
                @foreach($logs->getUrlRange(1, $logs->lastPage()) as $p => $url)
                    <a href="{{ $url }}" class="pg {{ $p === $logs->currentPage() ? 'on' : '' }}">{{ $p }}</a>
                @endforeach
                @if($logs->hasMorePages())
                    <a href="{{ $logs->nextPageUrl() }}" class="pg">
                        <i data-feather="chevron-right" style="width:12px;height:12px;"></i>
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>

@endsection

@push('scripts')
<script>
function togglePayload(id) {
    const drawer = document.getElementById('payload-' + id);
    drawer.classList.toggle('open');
}
</script>
@endpush
