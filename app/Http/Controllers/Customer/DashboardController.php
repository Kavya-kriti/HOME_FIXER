<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\ServiceCategory;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        // Stats for the summary cards
        $stats = [
            'total'       => ServiceRequest::where('customer_id', $user->id)->count(),
            'pending'     => ServiceRequest::where('customer_id', $user->id)
                                ->whereIn('status', ['pending', 'recommended'])->count(),
            'in_progress' => ServiceRequest::where('customer_id', $user->id)
                                ->whereIn('status', ['assigned', 'in_progress'])->count(),
            'completed'   => ServiceRequest::where('customer_id', $user->id)
                                ->where('status', 'completed')->count(),
        ];

        // 5 most recent requests for the activity feed
        $recentRequests = ServiceRequest::where('customer_id', $user->id)
            ->with(['service.category', 'jobAssignments.provider'])
            ->latest()
            ->take(5)
            ->get();

        // Categories for the "quick request" shortcut buttons
        $categories = ServiceCategory::where('is_active', true)->get();

        return view('customer.dashboard', compact('user', 'stats', 'recentRequests', 'categories'));
    }
}