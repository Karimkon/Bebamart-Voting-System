<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @deprecated Use County instead. Kept as alias because parishes table was renamed to counties.
 */
class Parish extends Model
{
    use SoftDeletes;

    protected $table = 'counties'; // table was renamed from parishes

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
