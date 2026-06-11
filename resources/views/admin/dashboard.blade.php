@extends('layouts.admin')
@section('title','Admin Dashboard')
@section('page-title','Platform Overview')

@push('styles')
<style>
    /* ── Stat grid ───────────────────────────────────────────────── */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: .85rem;
        margin-bottom: 1.25rem;
    }

    .kpi {
        background: var(--white);
        border: 1px solid var(--slate-200);
        border-radius: var(--radius);
        padding: 1rem 1.15rem;
        box-shadow: var(--shadow-sm);
    }

    .kpi-label {
        font-size: .675rem;
        font-weight: 600;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--slate-400);
        margin-bottom: .5rem;
    }

    .kpi-value {
        font-family: 'Instrument Serif', serif;
        font-size: 1.9rem;
        color: var(--slate-900);
        line-height: 1;
        margin-bottom: .2rem;
    }

    .kpi-sub { font-size: .75rem; color: var(--slate-400); }

    .kpi-icon {
        float: right;
        width: 32px; height: 32px;
        border-radius: 7px;
        display: flex; align-items: center; justify-content: center;
        margin-top: -2px;
    }

    .kpi-icon svg { width: 15px; height: 15px; }

    .ki-indigo { background: var(--indigo-dim); color: var(--indigo); }
    .ki-green  { background: var(--green-bg);   color: var(--green);  }
    .ki-amber  { background: var(--amber-bg);   color: var(--amber);  }
    .ki-blue   { background: var(--blue-bg);    color: var(--blue);   }

    /* ── Body grid ───────────────────────────────────────────────── */
    .body-grid {
        display: grid;
        grid-template-columns: 1fr 320px;
        gap: .85rem;
    }

    /* ── Mini bar chart ──────────────────────────────────────────── */
    .chart-wrap {
        padding: .85rem 1.1rem 1rem;
    }

    .bars {
        display: flex;
        align-items: flex-end;
        gap: .4rem;
        height: 100px;
        margin-bottom: .5rem;
    }

    .bar-col {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: .25rem;
        height: 100%;
        justify-content: flex-end;
    }

    .bar {
        width: 100%;
        border-radius: 4px 4px 0 0;
        background: var(--indigo);
        min-height: 4px;
        transition: height .5s ease;
        position: relative;
    }

    .bar:hover { opacity: .8; }

    .bar-tooltip {
        position: absolute;
        bottom: calc(100% + 4px);
        left: 50%; transform: translateX(-50%);
        background: var(--slate-900);
        color: var(--white);
        font-size: .65rem;
        padding: .2rem .45rem;
        border-radius: 4px;
        white-space: nowrap;
        pointer-events: none;
        opacity: 0;
        transition: opacity .12s;
    }

    .bar:hover .bar-tooltip { opacity: 1; }

    .bar-labels {
        display: flex;
        gap: .4rem;
    }

    .bar-label {
        flex: 1;
        text-align: center;
        font-size: .62rem;
        color: var(--slate-400);
    }

    /* ── AI stats widget ─────────────────────────────────────────── */
    .ai-stat-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .6rem;
        padding: .85rem 1.1rem;
    }

    .ai-stat {
        background: var(--slate-50);
        border: 1px solid var(--slate-200);
        border-radius: var(--radius-sm);
        padding: .6rem .75rem;
    }

    .ai-stat-val {
        font-family: 'Instrument Serif', serif;
        font-size: 1.3rem;
        color: var(--slate-900);
        line-height: 1;
        margin-bottom: .15rem;
    }

    .ai-stat-key { font-size: .68rem; color: var(--slate-400); }

    /* ── Pending providers list ──────────────────────────────────── */
    .pending-row {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .7rem 1.1rem;
        border-bottom: 1px solid var(--slate-200);
        transition: background .1s;
    }

    .pending-row:last-child { border-bottom: none; }
    .pending-row:hover { background: var(--slate-50); }

    .prov-avatar {
        width: 32px; height: 32px;
        border-radius: 7px;
        background: var(--slate-900);
        color: var(--white);
        font-family: 'Instrument Serif', serif;
        font-size: .9rem;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .prov-name  { font-size: .825rem; font-weight: 500; color: var(--slate-900); }
    .prov-email { font-size: .75rem; color: var(--slate-400); }

    /* ── Recent requests table ───────────────────────────────────── */
    .req-title { font-weight: 500; color: var(--slate-900); max-width: 220px; }
    .req-title-text { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .req-sub { font-size: .72rem; color: var(--slate-400); margin-top: .1rem; }

    @media (max-width: 1024px) {
        .kpi-grid  { grid-template-columns: repeat(2,1fr); }
        .body-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

{{-- ── KPI row ──────────────────────────────────────────────────────────── --}}
<div class="kpi-grid">
    <div class="kpi">
        <div class="kpi-icon ki-indigo"><i data-feather="users"></i></div>
        <div class="kpi-label">Total users</div>
        <div class="kpi-value">{{ number_format($stats['total_users']) }}</div>
        <div class="kpi-sub">{{ $stats['customers'] }} customers · {{ $stats['providers'] }} providers</div>
    </div>
    <div class="kpi">
        <div class="kpi-icon ki-blue"><i data-feather="clipboard"></i></div>
        <div class="kpi-label">Service requests</div>
        <div class="kpi-value">{{ number_format($stats['total_requests']) }}</div>
        <div class="kpi-sub">{{ $stats['pending_requests'] }} pending · {{ $stats['active_jobs'] }} active</div>
    </div>
    <div class="kpi">
        <div class="kpi-icon ki-green"><i data-feather="check-circle"></i></div>
        <div class="kpi-label">Jobs completed</div>
        <div class="kpi-value">{{ number_format($stats['completed_jobs']) }}</div>
        <div class="kpi-sub">Avg rating {{ $stats['avg_rating'] }} / 5.0</div>
    </div>
    <div class="kpi">
        <div class="kpi-icon ki-amber"><i data-feather="trending-up"></i></div>
        <div class="kpi-label">Total revenue</div>
        <div class="kpi-value">₹{{ number_format($stats['total_revenue'], 0) }}</div>
        <div class="kpi-sub">Across all completed jobs</div>
    </div>
</div>

{{-- ── Body grid ────────────────────────────────────────────────────────── --}}
<div class="body-grid">
    <div style="display:flex; flex-direction:column; gap:.85rem;">

        {{-- Requests trend chart --}}
        <div class="card">
            <div class="card-head">
                <span class="card-title">Request volume — last 7 days</span>
                <a href="{{ route('admin.requests') }}" style="font-size:.78rem; color:var(--indigo); text-decoration:none;">
                    View all →
                </a>
            </div>
            <div class="chart-wrap">
                @php $maxVal = max(array_values($trend) ?: [1]); @endphp
                <div class="bars">
                    @foreach($trend as $date => $count)
                        @php $pct = $maxVal > 0 ? round(($count / $maxVal) * 100) : 4; $pct = max($pct, 4); @endphp
                        <div class="bar-col">
                            <div class="bar" style="height:{{ $pct }}%;">
                                <span class="bar-tooltip">{{ $count }} request{{ $count !== 1 ? 's' : '' }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="bar-labels">
                    @foreach($trend as $date => $count)
                        <div class="bar-label">{{ \Carbon\Carbon::parse($date)->format('D') }}</div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Recent requests --}}
        <div class="card">
            <div class="card-head">
                <span class="card-title">Recent service requests</span>
                <a href="{{ route('admin.requests') }}" style="font-size:.78rem; color:var(--indigo); text-decoration:none;">
                    All requests →
                </a>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Request</th>
                        <th>Customer</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentRequests as $req)
                        @php
                            $statusMap = [
                                'pending'     => ['label'=>'Pending',     'cls'=>'b-amber'],
                                'recommended' => ['label'=>'AI Ready',    'cls'=>'b-blue'],
                                'assigned'    => ['label'=>'Assigned',    'cls'=>'b-indigo'],
                                'in_progress' => ['label'=>'In Progress', 'cls'=>'b-orange'],
                                'completed'   => ['label'=>'Completed',   'cls'=>'b-green'],
                                'cancelled'   => ['label'=>'Cancelled',   'cls'=>'b-red'],
                            ];
                            $s = $statusMap[$req->status] ?? ['label'=>ucfirst($req->status),'cls'=>'b-slate'];
                        @endphp
                        <tr>
                            <td style="color:var(--slate-400); font-size:.75rem;">#{{ $req->id }}</td>
                            <td>
                                <div class="req-title">
                                    <div class="req-title-text">{{ $req->title }}</div>
                                    <div class="req-sub">{{ $req->city }}</div>
                                </div>
                            </td>
                            <td>
                                <strong>{{ $req->customer->name }}</strong>
                                <div style="font-size:.72rem; color:var(--slate-400);">{{ $req->customer->email }}</div>
                            </td>
                            <td style="font-size:.8rem;">{{ $req->service?->category?->name ?? '—' }}</td>
                            <td><span class="badge {{ $s['cls'] }}">{{ $s['label'] }}</span></td>
                            <td style="font-size:.78rem; color:var(--slate-400); white-space:nowrap;">
                                {{ $req->created_at->format('d M Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">
                            <div class="empty"><i data-feather="inbox"></i><div class="empty-t">No requests yet</div></div>
                        </td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    {{-- ── Right column ────────────────────────────────────────── --}}
    <div style="display:flex; flex-direction:column; gap:.85rem;">

        {{-- AI stats --}}
        <div class="card">
            <div class="card-head">
                <span class="card-title">AI Engine</span>
                <a href="{{ route('admin.ai-logs') }}" style="font-size:.78rem; color:var(--indigo); text-decoration:none;">Logs →</a>
            </div>
            <div class="ai-stat-grid">
                <div class="ai-stat">
                    <div class="ai-stat-val">{{ number_format($aiStats['total']) }}</div>
                    <div class="ai-stat-key">Total calls</div>
                </div>
                <div class="ai-stat">
                    <div class="ai-stat-val">{{ $aiStats['today'] }}</div>
                    <div class="ai-stat-key">Today</div>
                </div>
                <div class="ai-stat">
                    <div class="ai-stat-val">{{ $aiStats['avg_ms'] }}<span style="font-size:.9rem; font-family:'Instrument Sans',sans-serif;">ms</span></div>
                    <div class="ai-stat-key">Avg response</div>
                </div>
                <div class="ai-stat">
                    <div class="ai-stat-val" style="{{ $aiStats['success_rate'] >= 90 ? 'color:var(--green)' : 'color:var(--amber)' }}">
                        {{ $aiStats['success_rate'] }}<span style="font-size:.9rem; font-family:'Instrument Sans',sans-serif;">%</span>
                    </div>
                    <div class="ai-stat-key">Success rate</div>
                </div>
            </div>
        </div>

        {{-- Pending verifications --}}
        <div class="card">
            <div class="card-head">
                <span class="card-title">Pending verification</span>
                @php $pvc = \App\Models\User::where('role','provider')->whereHas('providerProfile', fn($q)=>$q->whereNull('verified_at'))->count(); @endphp
                @if($pvc > 0)
                    <span class="badge b-amber">{{ $pvc }} waiting</span>
                @endif
            </div>

            @forelse($pendingProviders as $prov)
                <div class="pending-row">
                    <div class="prov-avatar">{{ strtoupper(substr($prov->name,0,1)) }}</div>
                    <div style="flex:1; min-width:0;">
                        <div class="prov-name">{{ $prov->name }}</div>
                        <div class="prov-email">{{ $prov->email }}</div>
                    </div>
                    <form method="POST" action="{{ route('admin.users.verify', $prov->id) }}">
                        @csrf
                        <button type="submit" class="btn btn-green" style="padding:.3rem .65rem; font-size:.72rem;">
                            <i data-feather="shield"></i> Verify
                        </button>
                    </form>
                </div>
            @empty
                <div class="empty" style="padding:1.5rem;">
                    <div class="empty-t">All providers verified</div>
                </div>
            @endforelse

            @if($pvc > 5)
                <div style="padding:.5rem 1.1rem; border-top:1px solid var(--slate-200);">
                    <a href="{{ route('admin.users', ['role'=>'provider']) }}" style="font-size:.78rem; color:var(--indigo); text-decoration:none;">
                        View all {{ $pvc }} pending →
                    </a>
                </div>
            @endif
        </div>

        {{-- Platform health --}}
        <div class="card" style="padding:1rem 1.1rem;">
            <div style="font-family:'Instrument Serif',serif; font-size:.9rem; margin-bottom:.75rem;">Platform health</div>
            @php
                $healthRows = [
                    ['label' => 'Pending requests',    'value' => $stats['pending_requests'], 'good' => $stats['pending_requests'] < 10],
                    ['label' => 'Active jobs',         'value' => $stats['active_jobs'],      'good' => true],
                    ['label' => 'AI success rate',     'value' => $aiStats['success_rate'].'%', 'good' => $aiStats['success_rate'] >= 90],
                    ['label' => 'Avg customer rating', 'value' => $stats['avg_rating'].'/5',  'good' => $stats['avg_rating'] >= 4],
                ];
            @endphp
            @foreach($healthRows as $row)
                <div style="display:flex; align-items:center; justify-content:space-between; padding:.45rem 0; border-bottom:1px solid var(--slate-200); font-size:.8rem;">
                    <span style="color:var(--slate-600);">{{ $row['label'] }}</span>
                    <div style="display:flex; align-items:center; gap:.5rem;">
                        <strong>{{ $row['value'] }}</strong>
                        <span style="width:7px; height:7px; border-radius:50%; background:{{ $row['good'] ? 'var(--green)' : 'var(--amber)' }};"></span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

@endsection
