@extends('layouts.admin')
@section('title','Users')
@section('page-title','User Management')

@push('styles')
<style>
    .role-tabs {
        display: flex; gap: .25rem;
        background: var(--white);
        border: 1px solid var(--slate-200);
        border-radius: var(--radius-sm);
        padding: .25rem;
        margin-bottom: 1rem;
        width: fit-content;
    }

    .role-tab {
        display: flex; align-items: center; gap: .4rem;
        padding: .35rem .8rem;
        border-radius: 5px;
        font-size: .78rem; font-weight: 500;
        text-decoration: none; color: var(--slate-500);
        transition: background .12s, color .12s;
    }

    .role-tab .rc {
        background: var(--slate-100); color: var(--slate-400);
        font-size: .65rem; padding: .05rem .35rem; border-radius: 99px;
    }

    .role-tab.on { background: var(--slate-900); color: var(--white); }
    .role-tab.on .rc { background: rgba(255,255,255,.15); color: rgba(255,255,255,.8); }
    .role-tab:not(.on):hover { background: var(--slate-100); }

    /* User row extras */
    .user-cell { display: flex; align-items: center; gap: .65rem; }

    .uavatar {
        width: 30px; height: 30px;
        border-radius: 7px;
        background: var(--slate-900);
        color: var(--white);
        font-family: 'Instrument Serif', serif;
        font-size: .85rem;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    .uavatar.customer { background: #78350F; }
    .uavatar.provider { background: #1E3A5F; }

    .u-name  { font-weight: 500; color: var(--slate-900); font-size: .825rem; }
    .u-email { font-size: .72rem; color: var(--slate-400); }

    .action-cell { display: flex; align-items: center; gap: .35rem; flex-wrap: wrap; }

    /* Rating stars */
    .stars { display:flex; gap:1px; align-items:center; }
    .stars svg { width:11px; height:11px; }

    /* Confirm-delete modal overlay */
    .confirm-overlay {
        display: none;
        position: fixed; inset: 0; z-index: 500;
        background: rgba(2,6,23,.5);
        align-items: center; justify-content: center;
    }

    .confirm-overlay.open { display: flex; }

    .confirm-box {
        background: var(--white);
        border: 1px solid var(--slate-200);
        border-radius: var(--radius);
        padding: 1.5rem;
        max-width: 380px;
        width: 90%;
        box-shadow: 0 20px 60px rgba(2,6,23,.3);
    }

    .confirm-title { font-family:'Instrument Serif',serif; font-size:1.1rem; margin-bottom:.4rem; }
    .confirm-body  { font-size:.825rem; color:var(--slate-600); margin-bottom:1.1rem; line-height:1.6; }
    .confirm-btns  { display:flex; gap:.5rem; justify-content:flex-end; }
</style>
@endpush

@section('content')

{{-- Role tabs --}}
@php $curRole = request('role', 'all'); @endphp
<div class="role-tabs">
    <a href="{{ route('admin.users') }}" class="role-tab {{ $curRole === 'all' ? 'on' : '' }}">
        All <span class="rc">{{ $roleCounts->sum() }}</span>
    </a>
    @foreach(['customer','provider','admin'] as $r)
        <a href="{{ route('admin.users', ['role'=>$r]) }}" class="role-tab {{ $curRole === $r ? 'on' : '' }}">
            {{ ucfirst($r) }} <span class="rc">{{ $roleCounts[$r] ?? 0 }}</span>
        </a>
    @endforeach
</div>

{{-- Main card --}}
<div class="card">
    {{-- Filter bar --}}
    <form method="GET" action="{{ route('admin.users') }}" class="filter-bar">
        @if(request('role'))
            <input type="hidden" name="role" value="{{ request('role') }}">
        @endif
        <div class="search-wrap">
            <i data-feather="search" class="search-icon"></i>
            <input type="text" name="search" class="search-input"
                   placeholder="Search name, email or phone..."
                   value="{{ request('search') }}">
        </div>
        <button type="submit" class="btn btn-indigo">
            <i data-feather="search"></i> Search
        </button>
        @if(request()->hasAny(['search','role']))
            <a href="{{ route('admin.users') }}" class="btn btn-ghost">
                <i data-feather="x"></i> Clear
            </a>
        @endif
    </form>

    {{-- Table --}}
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>User</th>
                <th>Role</th>
                <th>Phone</th>
                <th>Provider stats</th>
                <th>Status</th>
                <th>Joined</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $user)
                @php
                    $profile  = $user->providerProfile;
                    $isActive = !is_null($user->email_verified_at);
                @endphp
                <tr>
                    <td style="color:var(--slate-400); font-size:.75rem;">{{ $user->id }}</td>

                    <td>
                        <div class="user-cell">
                            <div class="uavatar {{ $user->role }}">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="u-name">{{ $user->name }}</div>
                                <div class="u-email">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>

                    <td>
                        @php $roleClsMap = ['customer'=>'b-blue','provider'=>'b-indigo','admin'=>'b-slate']; @endphp
                        <span class="badge {{ $roleClsMap[$user->role] ?? 'b-slate' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </td>

                    <td style="font-size:.8rem; color:var(--slate-500);">
                        {{ $user->phone ?? '—' }}
                    </td>

                    <td>
                        @if($profile)
                            <div class="stars" style="margin-bottom:.15rem;">
                                @for($s=1;$s<=5;$s++)
                                    <svg viewBox="0 0 24 24" style="{{ $s<=round($profile->avg_rating)?'fill:var(--amber);color:var(--amber)':'fill:none;color:var(--slate-200)' }}">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                    </svg>
                                @endfor
                            </div>
                            <div style="font-size:.72rem; color:var(--slate-400);">
                                {{ $profile->total_jobs }} jobs · {{ $profile->years_experience }}yr exp
                            </div>
                            @if(!$profile->verified_at)
                                <span class="badge b-amber" style="margin-top:.25rem;">Unverified</span>
                            @else
                                <span class="badge b-green" style="margin-top:.25rem;"><i data-feather="shield"></i> Verified</span>
                            @endif
                        @else
                            <span style="color:var(--slate-300); font-size:.8rem;">—</span>
                        @endif
                    </td>

                    <td>
                        <span class="badge {{ $isActive ? 'b-green' : 'b-red' }}">
                            {{ $isActive ? 'Active' : 'Suspended' }}
                        </span>
                    </td>

                    <td style="font-size:.78rem; color:var(--slate-400); white-space:nowrap;">
                        {{ $user->created_at->format('d M Y') }}
                    </td>

                    <td>
                        <div class="action-cell">
                            {{-- Provider verification --}}
                            @if($user->role === 'provider')
                                @if(!$profile?->verified_at)
                                    <form method="POST" action="{{ route('admin.users.verify', $user->id) }}">
                                        @csrf
                                        <button type="submit" class="btn btn-green">
                                            <i data-feather="shield"></i> Verify
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.users.revoke', $user->id) }}"
                                          onsubmit="return confirm('Revoke verification for {{ addslashes($user->name) }}?')">
                                        @csrf
                                        <button type="submit" class="btn btn-ghost" style="font-size:.72rem;">
                                            Revoke
                                        </button>
                                    </form>
                                @endif
                            @endif

                            {{-- Suspend/reactivate --}}
                            @if($user->id !== auth()->id() && $user->role !== 'admin')
                                <form method="POST" action="{{ route('admin.users.toggle', $user->id) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-ghost" style="font-size:.72rem;">
                                        {{ $isActive ? 'Suspend' : 'Reactivate' }}
                                    </button>
                                </form>

                                {{-- Delete --}}
                                <button type="button" class="btn btn-red" style="font-size:.72rem;"
                                        onclick="openDelete({{ $user->id }}, '{{ addslashes($user->name) }}')">
                                    <i data-feather="trash-2"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8">
                    <div class="empty">
                        <i data-feather="users"></i>
                        <div class="empty-t">No users found</div>
                        <div class="empty-s">Try adjusting your search or filter.</div>
                    </div>
                </td></tr>
            @endforelse
        </tbody>
    </table>

    {{-- Pagination --}}
    @if($users->hasPages())
        <div class="pag-row">
            <span>Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }}</span>
            <div class="pag-links">
                @if(!$users->onFirstPage())
                    <a href="{{ $users->previousPageUrl() }}" class="pg">
                        <i data-feather="chevron-left" style="width:12px;height:12px;"></i>
                    </a>
                @endif
                @foreach($users->getUrlRange(1, $users->lastPage()) as $p => $url)
                    <a href="{{ $url }}" class="pg {{ $p === $users->currentPage() ? 'on' : '' }}">{{ $p }}</a>
                @endforeach
                @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" class="pg">
                        <i data-feather="chevron-right" style="width:12px;height:12px;"></i>
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>

{{-- Delete confirm modal --}}
<div class="confirm-overlay" id="deleteModal">
    <div class="confirm-box">
        <div class="confirm-title">Delete user?</div>
        <p class="confirm-body">
            You are about to permanently delete <strong id="deleteUserName"></strong>.
            All their requests, jobs, and reviews will also be removed. This cannot be undone.
        </p>
        <div class="confirm-btns">
            <button type="button" class="btn btn-ghost" onclick="closeDelete()">Cancel</button>
            <form method="POST" id="deleteForm">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-red">
                    <i data-feather="trash-2"></i> Delete permanently
                </button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openDelete(id, name) {
    document.getElementById('deleteUserName').textContent = name;
    document.getElementById('deleteForm').action = '/admin/users/' + id;
    document.getElementById('deleteModal').classList.add('open');
    feather.replace({ 'stroke-width': 1.6 });
}

function closeDelete() {
    document.getElementById('deleteModal').classList.remove('open');
}

document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) closeDelete();
});
</script>
@endpush
