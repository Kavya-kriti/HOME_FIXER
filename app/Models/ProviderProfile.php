<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProviderProfile extends Model
{
    protected $fillable = [
        'user_id', 'bio', 'years_experience', 'service_radius_km',
        'hourly_rate', 'avg_rating', 'total_jobs', 'is_available',
        'id_proof_path', 'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'avg_rating'   => 'decimal:2',
            'hourly_rate'  => 'decimal:2',
            'is_available' => 'boolean',
            'verified_at'  => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isVerified(): bool
    {
        return !is_null($this->verified_at);
    }
}
