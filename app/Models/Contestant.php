<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contestant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'competition_id',
        'parish_id',
        'region_id',
        'current_round_id',
        'contestant_number',
        'full_name',
        'age',
        'profile_photo',
        'biography',
        'talent_description',
        'social_media_links',
        'total_votes',
        'current_round_votes',
        'ranking_position',
        'status',
        'is_promoted',
    ];

    protected function casts(): array
    {
        return [
            'age' => 'integer',
            'social_media_links' => 'array',
            'total_votes' => 'integer',
            'current_round_votes' => 'integer',
            'ranking_position' => 'integer',
            'is_promoted' => 'boolean',
        ];
    }

    /**
     * Get the competition that owns the contestant.
     */
    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    /**
     * Get the parish that owns the contestant.
     */
    public function parish()
    {
        return $this->belongsTo(Parish::class);
    }

    /**
     * Get the region that owns the contestant.
     */
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Get the current round for the contestant.
     */
    public function currentRound()
    {
        return $this->belongsTo(Round::class, 'current_round_id');
    }

    /**
     * Get the votes for the contestant.
     */
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * Get the media for the contestant.
     */
    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Scope a query to only include active contestants.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to filter by competition.
     */
    public function scopeByCompetition($query, $competitionId)
    {
        return $query->where('competition_id', $competitionId);
    }

    /**
     * Scope a query to filter by parish.
     */
    public function scopeByParish($query, $parishId)
    {
        return $query->where('parish_id', $parishId);
    }

    /**
     * Scope a query to order by total votes descending.
     */
    public function scopeTopVoted($query)
    {
        return $query->orderBy('total_votes', 'desc');
    }

    /**
     * Scope a query to order by ranking position.
     */
    public function scopeRanked($query)
    {
        return $query->orderBy('ranking_position', 'asc');
    }

    /**
     * Get the profile photo URL
     */
    public function getProfilePhotoUrlAttribute()
    {
        if ($this->profile_photo && file_exists(public_path($this->profile_photo))) {
            return asset($this->profile_photo);
        }
        
        return asset('images/default-contestant.png');
    }

    /**
     * Increment vote count
     */
    public function incrementVotes()
    {
        $this->increment('total_votes');
        $this->increment('current_round_votes');
    }
}
