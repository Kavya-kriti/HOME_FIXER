<?php
// app/Models/JobAssignment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobAssignment extends Model
{
    use HasFactory;

    // Added 'type' to the end of this array
    protected $fillable = [
        'request_id', 'provider_id', 'assigned_at', 'accepted_at',
        'started_at', 'completed_at', 'quoted_price', 'provider_notes', 'status', 'type',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at'   => 'datetime',
            'accepted_at'   => 'datetime',
            'started_at'    => 'datetime',
            'completed_at'  => 'datetime',
            'quoted_price'  => 'decimal:2',
        ];
    }

    public function serviceRequest()
    {
        return $this->belongsTo(ServiceRequest::class, 'request_id');
    }

    public function provider()
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    // Human-readable status badge attributes
    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'offered'  => ['label' => 'New Offer',   'color' => 'amber'],
            'accepted' => ['label' => 'Accepted',    'color' => 'blue'],
            'rejected' => ['label' => 'Declined',    'color' => 'red'],
            'started'  => ['label' => 'In Progress', 'color' => 'orange'],
            'done'     => ['label' => 'Completed',   'color' => 'green'],
            default    => ['label' => ucfirst($this->status), 'color' => 'gray'],
        };
    }
}