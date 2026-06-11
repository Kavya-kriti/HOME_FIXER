<?php
// app/Models/ServiceRequest.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'service_id', 'title', 'description',
        'address', 'city', 'pincode', 'latitude', 'longitude',
        'budget_min', 'budget_max', 'preferred_date', 'preferred_time',
        'status', 'ai_recommendation_payload',
    ];

    protected function casts(): array
    {
        return [
            'ai_recommendation_payload' => 'array',
            'preferred_date'            => 'date',
            'budget_min'                => 'decimal:2',
            'budget_max'                => 'decimal:2',
            'latitude'                  => 'decimal:7',
            'longitude'                 => 'decimal:7',
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────
    public function customer()      { return $this->belongsTo(User::class, 'customer_id'); }
    public function service()       { return $this->belongsTo(Service::class); }
    public function jobAssignments(){ return $this->hasMany(JobAssignment::class, 'request_id'); }
    public function reviews()       { return $this->hasMany(Review::class, 'request_id'); }

    // ── Status helpers ────────────────────────────────────────────────────────
    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'pending'     => ['label' => 'Pending',     'color' => 'amber'],
            'recommended' => ['label' => 'AI Ready',    'color' => 'blue'],
            'assigned'    => ['label' => 'Assigned',    'color' => 'indigo'],
            'in_progress' => ['label' => 'In Progress', 'color' => 'orange'],
            'completed'   => ['label' => 'Completed',   'color' => 'green'],
            'cancelled'   => ['label' => 'Cancelled',   'color' => 'red'],
            default       => ['label' => ucfirst($this->status), 'color' => 'gray'],
        };
    }
}
