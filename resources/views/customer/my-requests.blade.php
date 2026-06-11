@extends('layouts.customer')

@section('title', 'My Requests')
@section('page-title', 'My Requests')

@push('styles')
<style>
    /* ── Status tab bar ──────────────────────────────────────────── */
    .tab-bar {
        display: flex;
        gap: .25rem;
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: .3rem;
        margin-bottom: 1.5rem;
        width: fit-content;
    }

    .tab {
        padding: .45rem 1rem;
        border-radius: 6px;
        font-family: 'Syne', sans-serif;
        font-size: .8rem;
        font-weight: 600;
        color: var(--ink-2);
        text-decoration: none;
        transition: background .15s, color .15s;
        display: flex;
        align-items: center;
        gap: .4rem;
    }

    .tab .tab-count {
        background: var(--bg);
        color: var(--ink-3);
        font-size: .68rem;
        padding: .1rem .4rem;
        border-radius: 99px;
    }

    .tab.active {
        background: var(--sidebar-bg);
        color: #fff;
    }

    .tab.active .tab-count {
        background: rgba(255,255,255,.15);
        color: rgba(255,255,255,.8);
    }

    .tab:not(.active):hover { background: var(--bg); }

    /* ── Table ───────────────────────────────────────────────────── */
    .requests-table-wrap {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: .875rem;
    }

    thead th {
        padding: .85rem 1.25rem;
        text-align: left;
        font-family: 'Syne', sans-serif;
        font-size: .7rem;
        font-weight: 600;
        letter-spacing: .07em;
        text-transform: uppercase;
        color: var(--ink-3);
        border-bottom: 1px solid var(--border);
        background: var(--bg);
        white-space: nowrap;
    }

    tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background .12s;
        cursor: pointer;
    }

    tbody tr:last-child { border-bottom: none; }
    tbody tr:hover { background: rgba(224,123,57,.04); }

    td {
        padding: 1rem 1.25rem;
        vertical-align: middle;
    }

    .td-title {
        font-weight: 500;
        color: var(--ink);
        max-width: 240px;
    }

    .td-title-main {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin-bottom: .2rem;
    }

    .td-sub { font-size: .775rem; color: var(--ink-3); }

    .action-links { display: flex; gap: .5rem; align-items: center; }

    .action-link {
        padding: .35rem .75rem;
        border-radius: 6px;
        font-size: .78rem;
        font-family: 'Syne', sans-serif;
        font-weight: 600;
        text-decoration: none;
        transition: opacity .15s;
    }

    .action-link-primary {
        background: var(--accent);
        color: #fff;
    }

    .action-link-ghost {
        background: var(--bg);
        color: var(--ink-2);
        border: 1px solid var(--border);
    }

    .action-link:hover { opacity: .8; }

    .empty-table {
        padding: 4rem 2rem;
        text-align: center;
        color: var(--ink-3);
    }

    .empty-table svg { width: 48px; height: 48px; margin-bottom: 1rem; opacity: .3; }
    .empty-table-title { font-family: 'Syne', sans-serif; font-size: 1rem; color: var(--ink-2); margin-bottom: .4rem; }
    .empty-table-sub { font-size: .85rem; }

    /* ── Pagination ──────────────────────────────────────────────── */
    .pagination-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
        border-top: 1px solid var(--border);
        font-size: .82rem;
        color: var(--ink-3);
    }

    .pagination-links { display: flex; gap: .3rem; }

    .page-btn {
        width: 32px; height: 32px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 6px;
        font-family: 'Syne', sans-serif;
        font-size: .8rem;
        font-weight: 600;
        text-decoration: none;
        color: var(--ink-2);
        border: 1px solid var(--border);
        transition: background .12s, border-color .12s;
    }

    .page-btn:hover { border-color: var(--accent); color: var(--accent); }
    .page-btn.current { background: var(--sidebar-bg); color: #fff; border-color: var(--sidebar-bg); }
</style>
@endpush

@section('content')

{{-- Page header --}}
<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:1.25rem;">
    <div>
        <h2 style="font-family:'Syne',sans-serif; font-size:1.1rem; font-weight:700; color:var(--ink);">
            All Service Requests
        </h2>
        <p style="font-size:.825rem; color:var(--ink-3); margin-top:.2rem;">
            {{ $requests->total() }} total request{{ $requests->total() !== 1 ? 's' : '' }}
        </p>
    </div>
    <a href="{{ route('customer.request.create') }}" class="btn btn-accent">
        <i data-feather="plus"></i>
        New Request
    </a>
</div>

{{-- Status tabs --}}
<div class="tab-bar">
    @php
        $tabs = [
            'all'         => 'All',
            'pending'     => 'Pending',
            'recommended' => 'AI Ready',
            'assigned'    => 'Assigned',
            'in_progress' => 'In Progress',
            'completed'   => 'Completed',
            'cancelled'   => 'Cancelled',
        ];
        $currentStatus = request('status', 'all');
    @endphp

    @foreach($tabs as $key => $label)
        <a href="{{ route('customer.requests', $key !== 'all' ? ['status' => $key] : []) }}"
           class="tab {{ $currentStatus === $key ? 'active' : '' }}">
            {{ $label }}
            <span class="tab-count">
                {{ $key === 'all' ? $requests->total() : ($statusCounts[$key] ?? 0) }}
            </span>
        </a>
    @endforeach
</div>

{{-- Requests table --}}
<div class="requests-table-wrap">
    @if($requests->isEmpty())
        <div class="empty-table">
            <i data-feather="inbox"></i>
            <div class="empty-table-title">No requests found</div>
            <p class="empty-table-sub">
                @if($currentStatus !== 'all')
                    No {{ str_replace('_', ' ', $currentStatus) }} requests yet.
                    <a href="{{ route('customer.requests') }}" style="color:var(--accent);">View all</a>
                @else
                    You haven't made any service requests yet.
                    <a href="{{ route('customer.request.create') }}" style="color:var(--accent);">Make your first one →</a>
                @endif
            </p>
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Request</th>
                    <th>Service</th>
                    <th>Location</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $req)
                    @php $badge = $req->status_badge; @endphp
                    <tr onclick="window.location='{{ $req->status === 'recommended' ? route('customer.recommendation', $req->id) : '#' }}'">
                        <td style="color:var(--ink-3); font-size:.8rem; font-family:'Syne',sans-serif;">
                            #{{ $req->id }}
                        </td>
                        <td class="td-title">
                            <div class="td-title-main">{{ $req->title }}</div>
                            <div class="td-sub">{{ Str::limit($req->description, 60) }}</div>
                        </td>
                        <td>
                            <span style="font-size:.82rem; color:var(--ink-2);">
                                {{ $req->service?->name ?? '—' }}
                            </span>
                            @if($req->service?->category)
                                <div class="td-sub">{{ $req->service->category->name }}</div>
                            @endif
                        </td>
                        <td>
                            <span style="font-size:.82rem; color:var(--ink-2);">
                                {{ $req->city }}
                            </span>
                            @if($req->pincode)
                                <div class="td-sub">{{ $req->pincode }}</div>
                            @endif
                        </td>
                        <td style="font-size:.82rem; color:var(--ink-2); white-space:nowrap;">
                            {{ $req->created_at->format('d M Y') }}
                            <div class="td-sub">{{ $req->created_at->format('h:i A') }}</div>
                        </td>
                        <td>
                            <span class="badge badge-{{ $badge['color'] }}">{{ $badge['label'] }}</span>
                        </td>
                        <td onclick="event.stopPropagation()">
                            <div class="action-links">
                                @if($req->status === 'recommended')
                                    <a href="{{ route('customer.recommendation', $req->id) }}"
                                       class="action-link action-link-primary">
                                        View AI Result
                                    </a>
                                @elseif(in_array($req->status, ['assigned', 'in_progress']))
                                    <span class="action-link action-link-ghost" style="cursor:default;">
                                        Tracking
                                    </span>
                                @elseif($req->status === 'completed')
                                    @if($req->reviews->isEmpty())
                                        <a href="#" class="action-link action-link-primary">Leave Review</a>
                                    @else
                                        <span class="action-link action-link-ghost" style="cursor:default;">
                                            ★ Reviewed
                                        </span>
                                    @endif
                                @else
                                    <span class="action-link action-link-ghost" style="color:var(--ink-3); cursor:default;">—</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Pagination --}}
        @if($requests->hasPages())
        <div class="pagination-wrap">
            <span>
                Showing {{ $requests->firstItem() }}–{{ $requests->lastItem() }}
                of {{ $requests->total() }}
            </span>
            <div class="pagination-links">
                @if($requests->onFirstPage())
                    <span class="page-btn" style="opacity:.35; cursor:default;">
                        <i data-feather="chevron-left" style="width:14px;height:14px;"></i>
                    </span>
                @else
                    <a href="{{ $requests->previousPageUrl() }}" class="page-btn">
                        <i data-feather="chevron-left" style="width:14px;height:14px;"></i>
                    </a>
                @endif

                @foreach($requests->getUrlRange(1, $requests->lastPage()) as $page => $url)
                    <a href="{{ $url }}" class="page-btn {{ $page === $requests->currentPage() ? 'current' : '' }}">
                        {{ $page }}
                    </a>
                @endforeach

                @if($requests->hasMorePages())
                    <a href="{{ $requests->nextPageUrl() }}" class="page-btn">
                        <i data-feather="chevron-right" style="width:14px;height:14px;"></i>
                    </a>
                @else
                    <span class="page-btn" style="opacity:.35; cursor:default;">
                        <i data-feather="chevron-right" style="width:14px;height:14px;"></i>
                    </span>
                @endif
            </div>
        </div>
        @endif
    @endif
</div>

@endsection
