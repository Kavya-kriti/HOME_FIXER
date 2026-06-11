<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\JobAssignment;
use App\Models\ServiceRequest;
use App\Models\ProviderProfile;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JobController extends Controller
{
    // ── Available jobs (all offered assignments for this provider) ─────────────

    public function available(): View
    {
        $jobs = JobAssignment::where('provider_id', auth()->id())
            ->where('status', 'offered')
            ->with(['serviceRequest.customer', 'serviceRequest.service.category'])
            ->latest()
            ->paginate(10);

        return view('provider.available-jobs', compact('jobs'));
    }

    // ── My active + history jobs ───────────────────────────────────────────────

    public function myJobs(Request $request): View
    {
        $query = JobAssignment::where('provider_id', auth()->id())
            ->whereIn('status', ['accepted', 'started', 'done', 'rejected'])
            ->with(['serviceRequest.customer', 'serviceRequest.service.category', 'serviceRequest.reviews']);

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $jobs = $query->latest()->paginate(10);

        $counts = JobAssignment::where('provider_id', auth()->id())
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('provider.my-jobs', compact('jobs', 'counts'));
    }

    // ── Accept a job offer ────────────────────────────────────────────────────

    public function accept(JobAssignment $jobAssignment): RedirectResponse
    {
        $this->authoriseJob($jobAssignment);
        abort_if($jobAssignment->status !== 'offered', 422, 'This offer is no longer pending.');

        DB::transaction(function () use ($jobAssignment) {
            $jobAssignment->update([
                'status'      => 'accepted',
                'accepted_at' => now(),
            ]);
            $jobAssignment->serviceRequest->update(['status' => 'assigned']);
        });

        Log::info('Provider accepted job', [
            'provider_id' => auth()->id(),
            'assignment_id' => $jobAssignment->id,
        ]);

        return back()->with('success', 'Job accepted! The customer has been notified.');
    }

    // ── Reject a job offer ────────────────────────────────────────────────────

    public function reject(Request $request, JobAssignment $jobAssignment): RedirectResponse
    {
        $this->authoriseJob($jobAssignment);
        abort_if($jobAssignment->status !== 'offered', 422, 'This offer is no longer pending.');

        $jobAssignment->update([
            'status'         => 'rejected',
            'provider_notes' => $request->input('reason'),
        ]);

        // Put the request back to recommended so the customer can pick another provider
        $jobAssignment->serviceRequest->update(['status' => 'recommended']);

        return back()->with('success', 'Job declined. The customer can choose another provider.');
    }

    // ── Mark job as started ───────────────────────────────────────────────────

    public function start(JobAssignment $jobAssignment): RedirectResponse
    {
        $this->authoriseJob($jobAssignment);
        abort_if($jobAssignment->status !== 'accepted', 422);

        DB::transaction(function () use ($jobAssignment) {
            $jobAssignment->update([
                'status'     => 'started',
                'started_at' => now(),
            ]);
            $jobAssignment->serviceRequest->update(['status' => 'in_progress']);
        });

        return back()->with('success', 'Job marked as started. Good luck!');
    }

    // ── Mark job as complete ──────────────────────────────────────────────────

    public function complete(Request $request, JobAssignment $jobAssignment): RedirectResponse
    {
        $this->authoriseJob($jobAssignment);
        abort_if($jobAssignment->status !== 'started', 422);

        $validated = $request->validate([
            'quoted_price'   => ['nullable', 'numeric', 'min:0'],
            'provider_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($jobAssignment, $validated) {
            $jobAssignment->update([
                'status'         => 'done',
                'completed_at'   => now(),
                'quoted_price'   => $validated['quoted_price'] ?? $jobAssignment->quoted_price,
                'provider_notes' => $validated['provider_notes'] ?? $jobAssignment->provider_notes,
            ]);

            $jobAssignment->serviceRequest->update(['status' => 'completed']);

            // Update the provider's denormalised stats on their profile
            $profile = auth()->user()->providerProfile;
            if ($profile) {
                $profile->increment('total_jobs');

                // Recalculate avg_rating from the reviews table
                $newAvg = \App\Models\Review::where('reviewee_id', auth()->id())
                    ->avg('rating');
                if ($newAvg) {
                    $profile->update(['avg_rating' => round($newAvg, 2)]);
                }
            }
        });

        return redirect()->route('provider.my-jobs')
            ->with('success', 'Job marked as complete! Your stats have been updated.');
    }

    // ── Toggle availability ───────────────────────────────────────────────────

    public function toggleAvailability(): RedirectResponse
    {
        $profile = auth()->user()->providerProfile;
        abort_if(!$profile, 404, 'Provider profile not found.');

        $profile->update(['is_available' => !$profile->is_available]);

        $msg = $profile->is_available
            ? 'You are now visible to customers.'
            : 'You are now offline. No new jobs will be sent to you.';

        return back()->with('success', $msg);
    }

    // ── Set quoted price before accepting ─────────────────────────────────────

    public function setPrice(Request $request, JobAssignment $jobAssignment): RedirectResponse
    {
        $this->authoriseJob($jobAssignment);

        $validated = $request->validate([
            'quoted_price' => ['required', 'numeric', 'min:0'],
        ]);

        $jobAssignment->update(['quoted_price' => $validated['quoted_price']]);

        return back()->with('success', 'Price quote saved.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function authoriseJob(JobAssignment $job): void
    {
        abort_if($job->provider_id !== auth()->id(), 403, 'This job does not belong to you.');
    }
}
