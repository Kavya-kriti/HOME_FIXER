<?php

namespace App\Services;

use App\Models\ServiceRequest;
use App\Models\AiRecommendationLog;
use Illuminate\Support\Facades\Log;

class AiRecommendationService
{
    /**
     * Send a ServiceRequest to the Python AI engine and return
     * the structured recommendation payload.
     *
     * @throws \RuntimeException if the Python script fails
     */
    public function recommend(ServiceRequest $serviceRequest): array
    {
        $input = $this->buildInputPayload($serviceRequest);
        $startTime = microtime(true);

        try {
            $output = $this->callPythonScript($input);
            $responseMs = (int) ((microtime(true) - $startTime) * 1000);

            // Audit log — always record every AI call
            AiRecommendationLog::create([
                'request_id'      => $serviceRequest->id,
                'input_payload'   => $input,
                'output_payload'  => $output,
                'model_version'   => $output['model_version'] ?? 'v1.0',
                'response_time_ms' => $responseMs,
                'success'         => true,
            ]);

            return $output;

        } catch (\Exception $e) {
            AiRecommendationLog::create([
                'request_id'    => $serviceRequest->id,
                'input_payload' => $input,
                'success'       => false,
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    // ── Build the JSON payload sent to Python ─────────────────────────────────

    private function buildInputPayload(ServiceRequest $serviceRequest): array
    {
        $serviceRequest->loadMissing('service.category');

        return [
            'request_id'  => $serviceRequest->id,
            'title'       => $serviceRequest->title,
            'description' => $serviceRequest->description,
            'category'    => $serviceRequest->service?->category?->slug ?? null,
            'service_id'  => $serviceRequest->service_id,
            'city'        => $serviceRequest->city,
            'pincode'     => $serviceRequest->pincode,
            'latitude'    => $serviceRequest->latitude,
            'longitude'   => $serviceRequest->longitude,
            'budget_max'  => $serviceRequest->budget_max,
           // 'preferred_date' => $serviceRequest->preferred_date?->toDateString(),
           'preferred_date' => $serviceRequest->preferred_date
    ? \Carbon\Carbon::parse($serviceRequest->preferred_date)->toDateString()
    : null,
        ];
    }

    // ── Shell out to the Python recommender script ────────────────────────────

    private function callPythonScript(array $input): array
    {
        $scriptPath = base_path('ai_module/recommender.py');
        $pythonBin  = env('PYTHON_BIN', 'python3');

        // Pass the payload as a base64-encoded JSON argument to avoid shell injection
        $encoded = base64_encode(json_encode($input));
        $command = escapeshellcmd("$pythonBin $scriptPath") . ' ' . escapeshellarg($encoded);

        $output     = null;
        $returnCode = null;

        exec($command . ' 2>&1', $outputLines, $returnCode);
        $rawOutput = implode("\n", $outputLines);

        if ($returnCode !== 0) {
            Log::error('Python AI script returned non-zero exit code', [
                'exit_code' => $returnCode,
                'output'    => $rawOutput,
            ]);
            throw new \RuntimeException("AI engine error (exit $returnCode): $rawOutput");
        }

        $decoded = json_decode($rawOutput, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("AI engine returned invalid JSON: $rawOutput");
        }

        return $decoded;
    }
}
