<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class VoteOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'contestant_id',
        'competition_id',
        'package_id',
        'order_type',
        'votes_count',
        'amount',
        'currency',
        'price_per_vote',
        'merchant_reference',
        'pesapal_tracking_id',
        'payment_status',
        'votes_applied',
        'subscription_starts_at',
        'subscription_expires_at',
        'ip_address',
    ];

    protected function casts(): array
    {
        return [
            'votes_applied'           => 'boolean',
            'subscription_starts_at'  => 'datetime',
            'subscription_expires_at' => 'datetime',
            'amount'                  => 'decimal:2',
            'price_per_vote'          => 'decimal:4',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function contestant()
    {
        return $this->belongsTo(Contestant::class);
    }

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function package()
    {
        return $this->belongsTo(VotePackage::class, 'package_id');
    }

    // ── Type helpers ───────────────────────────────────────────────

    public function isBoost(): bool
    {
        return $this->order_type === 'vote_boost';
    }

    public function isPremium(): bool
    {
        return $this->order_type === 'premium_subscription';
    }

    // ── Apply purchased votes ──────────────────────────────────────

    /**
     * Apply purchased boost votes to contestant & competition totals.
     * Must be called inside a DB::transaction.
     */
    public function applyVotes(): void
    {
        if ($this->votes_applied) {
            return; // idempotent
        }

        if ($this->isBoost() && $this->contestant_id) {
            $this->contestant()->increment('total_votes', $this->votes_count);
            $this->competition()->increment('total_votes', $this->votes_count);
        }

        $this->update(['votes_applied' => true, 'payment_status' => 'completed']);
    }

    // ── Utility ────────────────────────────────────────────────────

    public static function generateMerchantRef(): string
    {
        return 'BEBA-' . strtoupper(Str::random(10));
    }
}
