<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AiRecommendationLog extends Model
{
    public $timestamps = false; // Only has created_at

    protected $fillable = [
        'request_id', 'input_payload', 'output_payload',
        'model_version', 'response_time_ms', 'success', 'error_message',
    ];

    protected function casts(): array
    {
        return [
            'input_payload'  => 'array',
            'output_payload' => 'array',
            'success'        => 'boolean',
            'created_at'     => 'datetime',
        ];
    }

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id');
    }
}
