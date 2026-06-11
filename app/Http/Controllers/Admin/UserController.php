<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ProviderProfile;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    // ── List all users with search + role filter ──────────────────────────────

    public function index(Request $request): View
    {
        $query = User::with('providerProfile')->latest();

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%$s%")
                  ->orWhere('email', 'like', "%$s%")
                  ->orWhere('phone', 'like', "%$s%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        $roleCounts = User::selectRaw('role, count(*) as count')
            ->groupBy('role')
            ->pluck('count', 'role');

        return view('admin.users', compact('users', 'roleCounts'));
    }

    // ── Verify a provider (admin stamp) ──────────────────────────────────────

    public function verifyProvider(User $user): RedirectResponse
    {
        abort_if($user->role !== 'provider', 422);

        $user->providerProfile?->update([
            'verified_at'  => now(),
            'is_available' => true,
        ]);

        Log::info('Admin verified provider', [
            'admin_id'    => auth()->id(),
            'provider_id' => $user->id,
        ]);

        return back()->with('success', "{$user->name} has been verified and is now visible to customers.");
    }

    // ── Revoke provider verification ──────────────────────────────────────────

    public function revokeProvider(User $user): RedirectResponse
    {
        abort_if($user->role !== 'provider', 422);

        $user->providerProfile?->update([
            'verified_at'  => null,
            'is_available' => false,
        ]);

        return back()->with('success', "{$user->name}'s verification has been revoked.");
    }

    // ── Toggle user active/suspended (soft approach: nullify verified_at) ─────

    public function toggleStatus(User $user): RedirectResponse
    {
        abort_if($user->id === auth()->id(), 422, 'You cannot suspend your own account.');
        abort_if($user->role === 'admin', 422, 'Admin accounts cannot be suspended here.');

        // We reuse email_verified_at as the "active" flag for simplicity
        if ($user->email_verified_at) {
            $user->update(['email_verified_at' => null]);
            $msg = "{$user->name} has been suspended.";
        } else {
            $user->update(['email_verified_at' => now()]);
            $msg = "{$user->name} has been reactivated.";
        }

        return back()->with('success', $msg);
    }

    // ── Hard-delete a user ────────────────────────────────────────────────────

    public function destroy(User $user): RedirectResponse
    {
        abort_if($user->id === auth()->id(), 422, 'You cannot delete your own account.');
        abort_if($user->role === 'admin', 422, 'Admin accounts cannot be deleted here.');

        $name = $user->name;
        $user->delete();

        Log::warning('Admin deleted user', [
            'admin_id'        => auth()->id(),
            'deleted_user_id' => $user->id,
            'deleted_name'    => $name,
        ]);

        return redirect()->route('admin.users')
            ->with('success', "{$name} has been permanently deleted.");
    }
}
