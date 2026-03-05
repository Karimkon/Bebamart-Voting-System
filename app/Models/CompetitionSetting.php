<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompetitionSetting extends Model
{
    protected $fillable = [
        'competition_id',
        'number_of_parishes',
        'contestants_per_parish',
        'number_of_rounds',
        'votes_per_user_per_day',
        'votes_per_contestant_per_day',
        'promotion_rules',
        'voting_rules',
        'require_social_login',
        'allowed_social_providers',
    ];

    protected function casts(): array
    {
        return [
            'number_of_parishes' => 'integer',
            'contestants_per_parish' => 'integer',
            'number_of_rounds' => 'integer',
            'votes_per_user_per_day' => 'integer',
            'votes_per_contestant_per_day' => 'integer',
            'promotion_rules' => 'array',
            'voting_rules' => 'array',
            'require_social_login' => 'boolean',
            'allowed_social_providers' => 'array',
        ];
    }

    /**
     * Get the competition that owns the settings.
     */
    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    /**
     * Get total contestants (calculated field)
     */
    public function getTotalContestantsAttribute()
    {
        return $this->number_of_parishes * $this->contestants_per_parish;
    }

    /**
     * Check if a social provider is allowed
     */
    public function isProviderAllowed(string $provider): bool
    {
        if (!$this->allowed_social_providers) {
            return true; // If no restrictions, allow all
        }
        
        return in_array($provider, $this->allowed_social_providers);
    }
}
