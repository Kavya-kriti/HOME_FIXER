<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use App\Models\Service;
use App\Models\ServiceRequest;
use App\Models\JobAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ServiceController extends Controller
{
    // ── Service requests overview ─────────────────────────────────────────────

    public function requests(Request $request): View
    {
        $query = ServiceRequest::with(['customer', 'service.category', 'jobAssignments.provider'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%$s%")
                  ->orWhere('description', 'like', "%$s%")
                  ->orWhere('city', 'like', "%$s%");
            });
        }

        $requests = $query->paginate(15)->withQueryString();

        $statusCounts = ServiceRequest::selectRaw('status, count(*) as count')
            ->groupBy('status')->pluck('count', 'status');

        return view('admin.services', compact('requests', 'statusCounts'));
    }

    // ── Manually assign a provider to a pending request ───────────────────────

   // ── Manually assign a provider to a pending request ───────────────────────

    public function assignProvider(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        abort_if(in_array($serviceRequest->status, ['completed', 'cancelled']), 422);

        $validated = $request->validate([
            'provider_id' => ['required', 'exists:users,id'],
        ]);

        // Remove any existing offered assignments first
        $serviceRequest->jobAssignments()
            ->where('status', 'offered')
            ->delete();

        // Give the database the 'type' it is asking for
        $serviceRequest->jobAssignments()->create([
            'provider_id' => $validated['provider_id'],
            'assigned_at' => now(),
            'status'      => 'offered',
            'type'        => 'manual', // <--- THIS IS THE FIX
        ]);

        $serviceRequest->update(['status' => 'assigned']);

        return back()->with('success', 'Provider manually assigned. They have been notified.');
    }

    // ── Cancel a service request ──────────────────────────────────────────────

    public function cancelRequest(ServiceRequest $serviceRequest): RedirectResponse
    {
        $serviceRequest->update(['status' => 'cancelled']);
        $serviceRequest->jobAssignments()
            ->whereIn('status', ['offered', 'accepted'])
            ->update(['status' => 'rejected']);

        return back()->with('success', "Request #{$serviceRequest->id} cancelled.");
    }

    // ── AI recommendation logs ────────────────────────────────────────────────

    public function aiLogs(Request $request): View
    {
        $logs = \App\Models\AiRecommendationLog::with('serviceRequest.customer')
            ->latest()
            ->paginate(20);

        return view('admin.ai-logs', compact('logs'));
    }
}
