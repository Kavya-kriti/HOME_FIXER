@extends('layouts.admin')
@section('title','Service Requests')
@section('page-title','Service Requests')

@push('styles')
<style>
    .status-tabs {
        display: flex; gap: .25rem; flex-wrap: wrap;
        background: var(--white);
        border: 1px solid var(--slate-200);
        border-radius: var(--radius-sm);
        padding: .25rem;
        margin-bottom: 1rem;
        width: fit-content;
    }

    .stab {
        display: flex; align-items: center; gap: .35rem;
        padding: .32rem .75rem;
        border-radius: 5px;
        font-size: .775rem; font-weight: 500;
        text-decoration: none; color: var(--slate-500);
        transition: background .12s, color .12s;
    }

    .stab .sc {
        background: var(--slate-100); color: var(--slate-400);
        font-size: .63rem; padding: .05rem .35rem; border-radius: 99px;
    }

    .stab.on { background: var(--slate-900); color: var(--white); }
    .stab.on .sc { background: rgba(255,255,255,.15); color: rgba(255,255,255,.8); }
    .stab:not(.on):hover { background: var(--slate-100); }

    /* Expand row for details + actions */
    .detail-panel {
        display: none;
        padding: 1rem 1.1rem;
        background: var(--slate-50);
        border-top: 1px solid var(--slate-200);
    }

    .detail-panel.open { display: block; }

    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .detail-label { font-size: .68rem; font-weight:600; letter-spacing:.08em; text-transform:uppercase; color:var(--slate-400); margin-bottom:.2rem; }
    .detail-val   { font-size: .825rem; color: var(--slate-700); line-height: 1.5; }

    .assign-form {
        display: flex; align-items: center; gap: .5rem;
        padding-top: .85rem;
        border-top: 1px solid var(--slate-200);
        flex-wrap: wrap;
    }

    .assign-select {
        padding: .45rem .75rem;
        border: 1px solid var(--slate-200);
        border-radius: var(--radius-sm);
        font-family: 'Instrument Sans', sans-serif;
        font-size: .825rem; color: var(--slate-900);
        background: var(--white); outline: none; cursor: pointer;
        min-width: 220px;
        transition: border-color .12s;
    }

    .assign-select:focus { border-color: var(--indigo); }

    .req-desc {
        font-size: .8rem; color: var(--slate-500); line-height: 1.55;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }

    .expand-btn {
        background: transparent; border: none; cursor: pointer;
        color: var(--slate-400); padding: 0;
        transition: color .12s;
    }
    .expand-btn:hover { color: var(--indigo); }
    .expand-btn svg { width: 15px; height: 15px; transition: transform .2s; }
    .expand-btn.open svg { transform: rotate(180deg); }
</style>
@endpush

@section('content')

@php
    $tabs = [
        'all'=>'All', 'pending'=>'Pending', 'recommended'=>'AI Ready',
        'assigned'=>'Assigned', 'in_progress'=>'In Progress',
        'completed'=>'Completed', 'cancelled'=>'Cancelled',
    ];
    $curStatus = request('status', 'all');
@endphp

<div class="status-tabs">
    @foreach($tabs as $k => $label)
        <a href="{{ route('admin.requests', $k !== 'all' ? ['status'=>$k] : []) }}"
           class="stab {{ $curStatus === $k ? 'on' : '' }}">
            {{ $label }}
            <span class="sc">{{ $k === 'all' ? $requests->total() : ($statusCounts[$k] ?? 0) }}</span>
        </a>
    @endforeach
</div>

<div class="card">
    {{-- Filter bar --}}
    <form method="GET" action="{{ route('admin.requests') }}" class="filter-bar">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <div class="search-wrap">
            <i data-feather="search" class="search-icon"></i>
            <input type="text" name="search" class="search-input"
                   placeholder="Search title, description, city..."
                   value="{{ request('search') }}">
        </div>
        <button type="submit" class="btn btn-indigo"><i data-feather="search"></i> Search</button>
        @if(request()->hasAny(['search','status']))
            <a href="{{ route('admin.requests') }}" class="btn btn-ghost"><i data-feather="x"></i> Clear</a>
        @endif
    </form>

    <table>
        <thead>
            <tr>
                <th style="width:30px;"></th>
                <th>#</th>
                <th>Request</th>
                <th>Customer</th>
                <th>Service</th>
                <th>Location</th>
                <th>Budget</th>
                <th>Status</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $req)
                @php
                    $smap = [
                        'pending'     => ['label'=>'Pending',     'cls'=>'b-amber'],
                        'recommended' => ['label'=>'AI Ready',    'cls'=>'b-blue'],
                        'assigned'    => ['label'=>'Assigned',    'cls'=>'b-indigo'],
                        'in_progress' => ['label'=>'In Progress', 'cls'=>'b-orange'],
                        'completed'   => ['label'=>'Completed',   'cls'=>'b-green'],
                        'cancelled'   => ['label'=>'Cancelled',   'cls'=>'b-red'],
                    ];
                    $s = $smap[$req->status] ?? ['label'=>ucfirst($req->status),'cls'=>'b-slate'];

                    // Active assignments
                    $activeAssignment = $req->jobAssignments->whereIn('status',['offered','accepted','started'])->first();

                    // Available providers for manual assignment
                    $availableProviders = \App\Models\User::where('role','provider')
                        ->whereHas('providerProfile', fn($q) => $q->where('is_available', true)->whereNotNull('verified_at'))
                        ->get();
                @endphp

                {{-- Main row --}}
                <tr id="row-{{ $req->id }}">
                    <td>
                        <button type="button" class="expand-btn" id="btn-{{ $req->id }}"
                                onclick="toggleDetail({{ $req->id }})">
                            <i data-feather="chevron-down"></i>
                        </button>
                    </td>
                    <td style="color:var(--slate-400); font-size:.75rem;">#{{ $req->id }}</td>
                    <td style="max-width:200px;">
                        <div style="font-weight:500; font-size:.825rem; color:var(--slate-900); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            {{ $req->title }}
                        </div>
                        <div class="req-desc">{{ $req->description }}</div>
                    </td>
                    <td>
                        <strong>{{ $req->customer->name }}</strong>
                        <div style="font-size:.72rem; color:var(--slate-400);">{{ $req->customer->email }}</div>
                    </td>
                    <td style="font-size:.8rem;">
                        {{ $req->service?->name ?? '—' }}
                        @if($req->service?->category)
                            <div style="font-size:.72rem; color:var(--slate-400);">{{ $req->service->category->name }}</div>
                        @endif
                    </td>
                    <td style="font-size:.8rem; color:var(--slate-600);">
                        {{ $req->city }}
                        @if($req->pincode)<div style="font-size:.72rem; color:var(--slate-400);">{{ $req->pincode }}</div>@endif
                    </td>
                    <td style="font-size:.8rem; white-space:nowrap;">
                        @if($req->budget_max)
                            ₹{{ number_format($req->budget_min ?? 0) }}–{{ number_format($req->budget_max) }}
                        @else
                            <span style="color:var(--slate-300);">—</span>
                        @endif
                    </td>
                    <td><span class="badge {{ $s['cls'] }}">{{ $s['label'] }}</span></td>
                    <td style="font-size:.75rem; color:var(--slate-400); white-space:nowrap;">
                        {{ $req->created_at->format('d M Y') }}
                    </td>
                    <td>
                        @if(!in_array($req->status, ['completed','cancelled']))
                            <form method="POST" action="{{ route('admin.requests.cancel', $req->id) }}"
                                  onsubmit="return confirm('Cancel request #{{ $req->id }}?')">
                                @csrf
                                <button type="submit" class="btn btn-red" style="font-size:.72rem; padding:.3rem .6rem;">
                                    <i data-feather="x"></i> Cancel
                                </button>
                            </form>
                        @else
                            <span style="color:var(--slate-300); font-size:.78rem;">—</span>
                        @endif
                    </td>
                </tr>

                {{-- Expandable detail panel --}}
                <tr>
                    <td colspan="10" style="padding:0; border:none;">
                        <div class="detail-panel" id="detail-{{ $req->id }}">
                            <div class="detail-grid">
                                <div>
                                    <div class="detail-label">Full description</div>
                                    <div class="detail-val">{{ $req->description }}</div>
                                </div>
                                <div>
                                    <div class="detail-label">Full address</div>
                                    <div class="detail-val">{{ $req->address ?? '—' }}</div>
                                    @if($req->preferred_date)
                                        <div style="margin-top:.5rem;">
                                            <div class="detail-label">Preferred date</div>
                                            <div class="detail-val">{{ \Carbon\Carbon::parse($req->preferred_date)->format('d M Y') }}</div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Current assignment --}}
                            @if($activeAssignment)
                                <div style="padding:.5rem .75rem; background:var(--blue-bg); border:1px solid var(--blue-border); border-radius:var(--radius-sm); font-size:.8rem; color:var(--blue); margin-bottom:.75rem; display:flex; align-items:center; gap:.5rem;">
                                    <i data-feather="user-check" style="width:14px;height:14px;"></i>
                                    Assigned to <strong>{{ $activeAssignment->provider->name }}</strong>
                                    · Status: {{ ucfirst($activeAssignment->status) }}
                                </div>
                            @endif

                            {{-- Manual assignment --}}
                            @if(!in_array($req->status, ['completed', 'cancelled']))
                                <form method="POST" action="{{ route('admin.requests.assign', $req->id) }}"
                                      class="assign-form">
                                    @csrf
                                    <span style="font-size:.8rem; font-weight:500; color:var(--slate-600);">
                                        Manually assign:
                                    </span>
                                    <select name="provider_id" class="assign-select" required>
                                        <option value="">— Select a verified provider —</option>
                                        @foreach($availableProviders as $prov)
                                            <option value="{{ $prov->id }}"
                                                    {{ $activeAssignment?->provider_id === $prov->id ? 'selected' : '' }}>
                                                {{ $prov->name }}
                                                (★ {{ number_format($prov->providerProfile?->avg_rating ?? 0, 1) }},
                                                {{ $prov->providerProfile?->total_jobs ?? 0 }} jobs)
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="btn btn-indigo">
                                        <i data-feather="user-plus"></i> Assign
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>

            @empty
                <tr><td colspan="10">
                    <div class="empty">
                        <i data-feather="clipboard"></i>
                        <div class="empty-t">No requests found</div>
                        <div class="empty-s">Try adjusting your filters.</div>
                    </div>
                </td></tr>
            @endforelse
        </tbody>
    </table>

    @if($requests->hasPages())
        <div class="pag-row">
            <span>Showing {{ $requests->firstItem() }}–{{ $requests->lastItem() }} of {{ $requests->total() }}</span>
            <div class="pag-links">
                @if(!$requests->onFirstPage())
                    <a href="{{ $requests->previousPageUrl() }}" class="pg">
                        <i data-feather="chevron-left" style="width:12px;height:12px;"></i>
                    </a>
                @endif
                @foreach($requests->getUrlRange(1, $requests->lastPage()) as $p => $url)
                    <a href="{{ $url }}" class="pg {{ $p === $requests->currentPage() ? 'on' : '' }}">{{ $p }}</a>
                @endforeach
                @if($requests->hasMorePages())
                    <a href="{{ $requests->nextPageUrl() }}" class="pg">
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
function toggleDetail(id) {
    const panel = document.getElementById('detail-' + id);
    const btn   = document.getElementById('btn-' + id);
    panel.classList.toggle('open');
    btn.classList.toggle('open');
    feather.replace({ 'stroke-width': 1.6 });
}
</script>
@endpush
