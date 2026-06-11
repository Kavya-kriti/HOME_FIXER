<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\JobAssignment;
use App\Models\ProviderProfile;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user    = auth()->user();
        $profile = $user->providerProfile;

        $stats = [
            'offered'    => JobAssignment::where('provider_id', $user->id)->where('status', 'offered')->count(),
            'active'     => JobAssignment::where('provider_id', $user->id)->whereIn('status', ['accepted', 'started'])->count(),
            'completed'  => JobAssignment::where('provider_id', $user->id)->where('status', 'done')->count(),
            'earnings'   => JobAssignment::where('provider_id', $user->id)->where('status', 'done')
                                ->whereNotNull('quoted_price')->sum('quoted_price'),
        ];

        // 5 most recent assignments for the activity strip
        $recentJobs = JobAssignment::where('provider_id', $user->id)
            ->with(['serviceRequest.customer', 'serviceRequest.service.category'])
            ->latest()
            ->take(5)
            ->get();

        // New job offers that need a response
        $newOffers = JobAssignment::where('provider_id', $user->id)
            ->where('status', 'offered')
            ->with(['serviceRequest.customer', 'serviceRequest.service.category'])
            ->latest()
            ->get();

        return view('provider.dashboard', compact('user', 'profile', 'stats', 'recentJobs', 'newOffers'));
    }
}
