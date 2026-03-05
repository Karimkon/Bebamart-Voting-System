<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class Vote extends Model
{
    protected $fillable = [
        'user_id',
        'contestant_id',
        'competition_id',
        'round_id',
        'ip_address',
        'device_hash',
        'user_agent',
        'vote_date',
        'voted_at',
        'status',
        'fraud_notes',
        'vote_source',
        'vote_order_id',
    ];

    protected function casts(): array
    {
        return [
            'vote_date' => 'date',
            'voted_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vote) {
            $vote->voted_at = now();
            $vote->vote_date = now()->toDateString();
            
            // Generate device hash if not provided
            if (empty($vote->device_hash)) {
                $vote->device_hash = self::generateDeviceHash(
                    $vote->ip_address, 
                    $vote->user_agent
                );
            }
        });
    }

    /**
     * Get the user that owns the vote.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the contestant that owns the vote.
     */
    public function contestant()
    {
        return $this->belongsTo(Contestant::class);
    }

    /**
     * Get the competition that owns the vote.
     */
    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    /**
     * Get the round that owns the vote.
     */
    public function round()
    {
        return $this->belongsTo(Round::class);
    }

    /**
     * Get the vote order that generated this vote (premium votes).
     */
    public function voteOrder()
    {
        return $this->belongsTo(VoteOrder::class, 'vote_order_id');
    }

    /**
     * Scope a query to only include valid votes.
     */
    public function scopeValid($query)
    {
        return $query->where('status', 'valid');
    }

    /**
     * Scope a query to only include suspicious votes.
     */
    public function scopeSuspicious($query)
    {
        return $query->where('status', 'suspicious');
    }

    /**
     * Scope a query to only include flagged votes.
     */
    public function scopeFlagged($query)
    {
        return $query->where('status', 'flagged');
    }

    /**
     * Scope a query to filter votes by date.
     */
    public function scopeByDate($query, $date)
    {
        return $query->whereDate('vote_date', $date);
    }

    /**
     * Scope to get today's votes
     */
    public function scopeToday($query)
    {
        return $query->whereDate('vote_date', now()->toDateString());
    }

    /**
     * Generate device hash from IP and user agent
     */
    public static function generateDeviceHash($ipAddress, $userAgent): string
    {
        return hash('sha256', $ipAddress . '|' . $userAgent);
    }

    /**
     * Check if vote is valid
     */
    public function isValid(): bool
    {
        return $this->status === 'valid';
    }

    /**
     * Mark vote as suspicious
     */
    public function markAsSuspicious(string $reason = null)
    {
        $this->update([
            'status' => 'suspicious',
            'fraud_notes' => $reason,
        ]);
    }

    /**
     * Mark vote as flagged
     */
    public function markAsFlagged(string $reason = null)
    {
        $this->update([
            'status' => 'flagged',
            'fraud_notes' => $reason,
        ]);
    }
}
