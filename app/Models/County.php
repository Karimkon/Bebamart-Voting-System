<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class County extends Model
{
    use SoftDeletes;

    protected $table = 'counties';

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

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function contestants()
    {
        return $this->hasMany(Contestant::class, 'county_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRegion($query, $regionId)
    {
        return $query->where('region_id', $regionId);
    }
}
