<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'category_id', 'name', 'description',
        'base_price', 'duration_estimate_hrs', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'base_price'            => 'decimal:2',
            'duration_estimate_hrs' => 'decimal:2',
        ];
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }

    public function providerServices()
    {
        return $this->hasMany(ProviderService::class);
    }
}