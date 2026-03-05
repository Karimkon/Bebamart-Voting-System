<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Region extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
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
     * Get the parishes for the region.
     */
    public function parishes()
    {
        return $this->hasMany(Parish::class);
    }

    /**
     * Get the contestants for the region.
     */
    public function contestants()
    {
        return $this->hasMany(Contestant::class);
    }

    /**
     * Scope a query to only include active regions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
