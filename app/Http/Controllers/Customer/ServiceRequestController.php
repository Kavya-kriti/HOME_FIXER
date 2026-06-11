<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Requests\ServiceRequestFormRequest;
use App\Models\ServiceRequest;
use App\Models\ServiceCategory;
use App\Models\Service;
use App\Services\AiRecommendationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class ServiceRequestController extends Controller
{
    public function __construct(
        private AiRecommendationService $aiService
    ) {}

    // ── Show the new-request form ─────────────────────────────────────────────

    public function create(Request $request): View
    {
        $categories = ServiceCategory::where('is_active', true)->with('services')->get();

        // Pre-select category if coming from a quick-action button
        $selectedCategory = $request->query('category');

        return view('customer.request-service', compact('categories', 'selectedCategory'));
    }

    // ── Store request + trigger AI ────────────────────────────────────────────

    public function store(ServiceRequestFormRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // 1. Persist the service request
        $serviceRequest = ServiceRequest::create([
            'customer_id' => auth()->id(),
            'service_id'  => $validated['service_id'] ?? null,
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'address'     => $validated['address'],
            'city'        => $validated['city'],
            'pincode'     => $validated['pincode'],
            'latitude'    => $validated['latitude'] ?? null,
            'longitude'   => $validated['longitude'] ?? null,
            'budget_min'  => $validated['budget_min'] ?? null,
            'budget_max'  => $validated['budget_max'] ?? null,
            'preferred_date' => $validated['preferred_date'] ?? null,
            'preferred_time' => $validated['preferred_time'] ?? null,
            'status'      => 'pending',
        ]);

        // 2. Call the AI recommendation engine
        try {
            $recommendation = $this->aiService->recommend($serviceRequest);

            $serviceRequest->update([
                'ai_recommendation_payload' => $recommendation,
                'status'                    => 'recommended',
            ]);

            Log::info('AI recommendation succeeded', [
                'request_id' => $serviceRequest->id,
                'confidence' => $recommendation['confidence'] ?? null,
            ]);

            return redirect()
                ->route('customer.recommendation', $serviceRequest->id)
                ->with('success', 'We found the best matches for your request!');

        } catch (\Exception $e) {
            // AI failure is non-fatal — request is saved, admin can assign manually
            Log::error('AI recommendation failed', [
                'request_id' => $serviceRequest->id,
                'error'      => $e->getMessage(),
            ]);

            return redirect()
                ->route('customer.requests')
                ->with('warning', 'Your request was saved. Our team will assign a provider shortly.');
        }
    }

    // ── Show all customer requests ────────────────────────────────────────────

    public function index(Request $request): View
    {
        $query = ServiceRequest::where('customer_id', auth()->id())
            ->with(['service.category', 'jobAssignments.provider', 'reviews']);

        // Optional status filter from the tab bar
        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $requests = $query->latest()->paginate(10);

        $statusCounts = ServiceRequest::where('customer_id', auth()->id())
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return view('customer.my-requests', compact('requests', 'statusCounts'));
    }

    // ── Show AI recommendation result ─────────────────────────────────────────

    public function showRecommendation(ServiceRequest $serviceRequest): View
    {
        // Make sure the customer can only see their own requests
        abort_if($serviceRequest->customer_id !== auth()->id(), 403);

        $serviceRequest->load(['service.category', 'jobAssignments.provider.providerProfile']);

        $payload = $serviceRequest->ai_recommendation_payload;

        return view('customer.recommendation-result', compact('serviceRequest', 'payload'));
    }

    // ── Customer accepts a recommended provider ───────────────────────────────

    public function acceptProvider(Request $request, ServiceRequest $serviceRequest): RedirectResponse
    {
        abort_if($serviceRequest->customer_id !== auth()->id(), 403);
        abort_if($serviceRequest->status !== 'recommended', 422);

        $validated = $request->validate([
            'provider_id' => ['required', 'exists:users,id'],
        ]);

        // Create the job assignment
        $serviceRequest->jobAssignments()->create([
            'provider_id' => $validated['provider_id'],
            'assigned_at' => now(),
            'status'      => 'offered',
        ]);

        $serviceRequest->update(['status' => 'assigned']);

        return redirect()
            ->route('customer.requests')
            ->with('success', 'Provider notified! They will confirm your booking shortly.');
    }
}
