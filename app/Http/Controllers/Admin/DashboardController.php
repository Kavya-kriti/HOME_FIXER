<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ServiceRequest;
use App\Models\JobAssignment;
use App\Models\AiRecommendationLog;
use App\Models\Review;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // ── Platform-wide headline stats ──────────────────────────────────────
        $stats = [
            'total_users'      => User::count(),
            'customers'        => User::where('role', 'customer')->count(),
            'providers'        => User::where('role', 'provider')->count(),
            'total_requests'   => ServiceRequest::count(),
            'pending_requests' => ServiceRequest::whereIn('status', ['pending', 'recommended'])->count(),
            'active_jobs'      => JobAssignment::whereIn('status', ['accepted', 'started'])->count(),
            'completed_jobs'   => JobAssignment::where('status', 'done')->count(),
            'ai_calls_today'   => AiRecommendationLog::whereDate('created_at', today())->count(),
            'ai_success_rate'  => $this->aiSuccessRate(),
            'avg_rating'       => round(Review::avg('rating') ?? 0, 2),
            'total_revenue'    => JobAssignment::where('status', 'done')->sum('quoted_price'),
        ];

        // ── Requests over last 7 days (for mini chart) ────────────────────────
        $requestTrend = ServiceRequest::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date')
            ->toArray();

        // Fill in any missing days with 0
        $trend = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = now()->subDays($i)->format('Y-m-d');
            $trend[$day] = $requestTrend[$day] ?? 0;
        }

        // ── Recent activity feed ──────────────────────────────────────────────
        $recentRequests = ServiceRequest::with(['customer', 'service.category'])
            ->latest()->take(8)->get();

        // ── Providers pending verification ────────────────────────────────────
        $pendingProviders = User::where('role', 'provider')
            ->whereHas('providerProfile', fn($q) => $q->whereNull('verified_at'))
            ->with('providerProfile')
            ->latest()->take(5)->get();

        // ── AI log summary ────────────────────────────────────────────────────
        $aiStats = [
            'total'        => AiRecommendationLog::count(),
            'today'        => AiRecommendationLog::whereDate('created_at', today())->count(),
            'avg_ms'       => (int) AiRecommendationLog::avg('response_time_ms'),
            'success_rate' => $this->aiSuccessRate(),
        ];

        return view('admin.dashboard', compact(
            'stats', 'trend', 'recentRequests', 'pendingProviders', 'aiStats'
        ));
    }

    private function aiSuccessRate(): float
    {
        $total   = AiRecommendationLog::count();
        $success = AiRecommendationLog::where('success', true)->count();
        return $total > 0 ? round(($success / $total) * 100, 1) : 0;
    }
}
