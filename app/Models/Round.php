<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Round extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'competition_id',
        'name',
        'description',
        'round_number',
        'start_date',
        'end_date',
        'status',
        'total_votes',
        'qualified_contestants',
        'promotion_criteria',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'round_number' => 'integer',
            'total_votes' => 'integer',
            'qualified_contestants' => 'integer',
            'promotion_criteria' => 'array',
        ];
    }

    /**
     * Get the competition that owns the round.
     */
    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    /**
     * Get the contestants in this round.
     */
    public function contestants()
    {
        return $this->hasMany(Contestant::class, 'current_round_id');
    }

    /**
     * Get the votes for this round.
     */
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * Scope a query to only include active rounds.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include completed rounds.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Check if round is currently active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' 
            && now()->between($this->start_date, $this->end_date);
    }

    /**
     * Scope to order by round number
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('round_number');
    }
}
