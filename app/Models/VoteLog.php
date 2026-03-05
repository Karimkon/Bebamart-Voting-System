<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoteLog extends Model
{
    protected $fillable = [
        'vote_id',
        'user_id',
        'contestant_id',
        'competition_id',
        'action',
        'ip_address',
        'device_hash',
        'user_agent',
        'details',
        'fraud_indicators',
    ];

    protected function casts(): array
    {
        return [
            'fraud_indicators' => 'array',
        ];
    }

    /**
     * Get the vote that owns the log.
     */
    public function vote()
    {
        return $this->belongsTo(Vote::class);
    }

    /**
     * Get the user that owns the log.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the contestant that owns the log.
     */
    public function contestant()
    {
        return $this->belongsTo(Contestant::class);
    }

    /**
     * Get the competition that owns the log.
     */
    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    /**
     * Scope a query to filter by action.
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope a query to filter by IP address.
     */
    public function scopeByIp($query, $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    /**
     * Scope a query to filter by device hash.
     */
    public function scopeByDevice($query, $deviceHash)
    {
        return $query->where('device_hash', $deviceHash);
    }

    /**
     * Create a log entry for vote casting
     */
    public static function logVoteCast($vote)
    {
        return self::create([
            'vote_id' => $vote->id,
            'user_id' => $vote->user_id,
            'contestant_id' => $vote->contestant_id,
            'competition_id' => $vote->competition_id,
            'action' => 'vote_cast',
            'ip_address' => $vote->ip_address,
            'device_hash' => $vote->device_hash,
            'user_agent' => $vote->user_agent,
            'details' => 'Vote successfully cast',
        ]);
    }

    /**
     * Create a log entry for flagged vote
     */
    public static function logVoteFlagged($vote, $fraudIndicators = [])
    {
        return self::create([
            'vote_id' => $vote->id,
            'user_id' => $vote->user_id,
            'contestant_id' => $vote->contestant_id,
            'competition_id' => $vote->competition_id,
            'action' => 'vote_flagged',
            'ip_address' => $vote->ip_address,
            'device_hash' => $vote->device_hash,
            'user_agent' => $vote->user_agent,
            'details' => 'Vote flagged as suspicious',
            'fraud_indicators' => $fraudIndicators,
        ]);
    }
}
