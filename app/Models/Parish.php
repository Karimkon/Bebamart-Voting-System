<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Parish extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'region_id',
        'district',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the region that owns the parish.
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Get the contestants for the parish.
     */
    public function contestants()
    {
        return $this->hasMany(Contestant::class);
    }

    /**
     * Scope a query to only include active parishes.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to filter by region.
     */
    public function scopeByRegion($query, $regionId)
    {
        return $query->where('region_id', $regionId);
    }
}
