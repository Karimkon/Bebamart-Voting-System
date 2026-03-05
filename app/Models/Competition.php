<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Competition extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'type',
        'description',
        'rules',
        'banner_image',
        'logo',
        'start_date',
        'end_date',
        'status',
        'voting_enabled',
        'total_votes',
        'total_contestants',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'voting_enabled' => 'boolean',
            'total_votes' => 'integer',
            'total_contestants' => 'integer',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($competition) {
            if (empty($competition->slug)) {
                $competition->slug = Str::slug($competition->name);
            }
        });
    }

    /**
     * Get the settings for the competition.
     */
    public function settings()
    {
        return $this->hasOne(CompetitionSetting::class);
    }

    /**
     * Get the rounds for the competition.
     */
    public function rounds()
    {
        return $this->hasMany(Round::class);
    }

    /**
     * Get the contestants for the competition.
     */
    public function contestants()
    {
        return $this->hasMany(Contestant::class);
    }

    /**
     * Get the votes for the competition.
     */
    public function votes()
    {
        return $this->hasMany(Vote::class);
    }

    /**
     * Get the media for the competition.
     */
    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Get the active round for the competition.
     */
    public function activeRound()
    {
        return $this->hasOne(Round::class)->where('status', 'active');
    }

    /**
     * Scope a query to only include active competitions.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include upcoming competitions.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('status', 'upcoming');
    }

    /**
     * Scope a query to only include competitions with voting enabled.
     */
    public function scopeVotingEnabled($query)
    {
        return $query->where('voting_enabled', true);
    }

    /**
     * Check if voting is currently open
     */
    public function isVotingOpen(): bool
    {
        return $this->voting_enabled 
            && $this->status === 'active' 
            && now()->between($this->start_date, $this->end_date);
    }

    /**
     * Get route key name for route model binding
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }
}
