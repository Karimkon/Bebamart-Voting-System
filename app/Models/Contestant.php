<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contestant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'competition_id',
        'county_id',
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
            'age'                 => 'integer',
            'social_media_links'  => 'array',
            'total_votes'         => 'integer',
            'current_round_votes' => 'integer',
            'ranking_position'    => 'integer',
            'is_promoted'         => 'boolean',
        ];
    }

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function county()
    {
        return $this->belongsTo(County::class, 'county_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function currentRound()
    {
        return $this->belongsTo(Round::class, 'current_round_id');
    }

    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByCompetition($query, $competitionId)
    {
        return $query->where('competition_id', $competitionId);
    }

    public function scopeByCounty($query, $countyId)
    {
        return $query->where('county_id', $countyId);
    }

    public function scopeTopVoted($query)
    {
        return $query->orderBy('total_votes', 'desc');
    }

    public function scopeRanked($query)
    {
        return $query->orderBy('ranking_position', 'asc');
    }

    public function getProfilePhotoUrlAttribute()
    {
        return $this->profile_photo
            ? asset($this->profile_photo)
            : asset('images/default-contestant.png');
    }

    public function incrementVotes()
    {
        $this->increment('total_votes');
        $this->increment('current_round_votes');
    }
}