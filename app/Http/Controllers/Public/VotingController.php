<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Vote;
use App\Models\VoteLog;
use App\Models\VoteOrder;
use App\Models\Contestant;
use App\Models\Competition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VotingController extends Controller
{
    /**
     * Cast a vote for a contestant
     */
    public function vote(Request $request, Contestant $contestant)
    {
        if (!auth()->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to vote',
            ], 401);
        }

        $user = auth()->user();
        $competition = $contestant->competition;

        // Check if voting is open
        if (!$competition->isVotingOpen()) {
            return response()->json([
                'success' => false,
                'message' => 'Voting is currently closed for this competition',
            ], 403);
        }

        // Check if contestant is active
        if ($contestant->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'This contestant is not eligible for voting',
            ], 403);
        }

        // Check premium subscription
        $hasPremium = VoteOrder::where('user_id', $user->id)
            ->where('competition_id', $competition->id)
            ->where('order_type', 'premium_subscription')
            ->where('payment_status', 'completed')
            ->where('subscription_starts_at', '<=', now())
            ->where('subscription_expires_at', '>=', now())
            ->exists();

        $dailyVoteCount = Vote::where('user_id', $user->id)
            ->where('competition_id', $competition->id)
            ->whereDate('vote_date', now()->toDateString())
            ->count();

        if ($hasPremium) {
            // Premium: up to 10 total votes per day across all contestants
            if ($dailyVoteCount >= 10) {
                return response()->json([
                    'success' => false,
                    'message' => 'Daily premium vote limit (10) reached. Come back tomorrow!',
                ], 403);
            }
        } else {
            // Free: 1 vote per contestant per day
            $existingVote = Vote::where('user_id', $user->id)
                ->where('contestant_id', $contestant->id)
                ->whereDate('vote_date', now()->toDateString())
                ->first();

            if ($existingVote) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already voted for this contestant today',
                ], 403);
            }
        }

        // Get IP and device info
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();
        $deviceHash = Vote::generateDeviceHash($ipAddress, $userAgent);

        // Fraud detection
        $fraudIndicators = $this->detectFraud($user, $contestant, $ipAddress, $deviceHash);

        $voteStatus = empty($fraudIndicators) ? 'valid' : 'suspicious';

        // Create vote
        try {
            DB::beginTransaction();

            $vote = Vote::create([
                'user_id'        => $user->id,
                'contestant_id'  => $contestant->id,
                'competition_id' => $competition->id,
                'round_id'       => $contestant->current_round_id,
                'ip_address'     => $ipAddress,
                'device_hash'    => $deviceHash,
                'user_agent'     => $userAgent,
                'status'         => $voteStatus,
                'vote_source'    => $hasPremium ? 'premium' : 'free',
            ]);

            // Increment contestant votes (only for valid votes)
            if ($voteStatus === 'valid') {
                $contestant->incrementVotes();
                $competition->increment('total_votes');
            }

            // Log the vote
            if ($voteStatus === 'valid') {
                VoteLog::logVoteCast($vote);
            } else {
                VoteLog::logVoteFlagged($vote, $fraudIndicators);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Vote cast successfully!',
                'vote_count' => $contestant->fresh()->total_votes,
                'status' => $voteStatus,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to cast vote. Please try again.',
            ], 500);
        }
    }

    /**
     * Detect potential fraud indicators
     */
    protected function detectFraud($user, $contestant, $ipAddress, $deviceHash)
    {
        $indicators = [];

        // Check votes from same IP in short time
        $ipVotes = Vote::where('ip_address', $ipAddress)
            ->where('voted_at', '>=', now()->subMinutes(5))
            ->count();

        if ($ipVotes > 10) {
            $indicators[] = 'rapid_voting_same_ip';
        }

        // Check votes from same device in short time
        $deviceVotes = Vote::where('device_hash', $deviceHash)
            ->where('voted_at', '>=', now()->subMinutes(5))
            ->count();

        if ($deviceVotes > 10) {
            $indicators[] = 'rapid_voting_same_device';
        }

        // Check total votes by user today
        $userVotesToday = Vote::where('user_id', $user->id)
            ->whereDate('vote_date', now()->toDateString())
            ->count();

        if ($userVotesToday > 50) {
            $indicators[] = 'excessive_daily_votes';
        }

        // Check for pattern: voting for same contestant repeatedly
        $contestantVotes = Vote::where('user_id', $user->id)
            ->where('contestant_id', $contestant->id)
            ->count();

        if ($contestantVotes > 20) {
            $indicators[] = 'repeated_contestant_voting';
        }

        return $indicators;
    }

    /**
     * Get vote status for a contestant
     */
    public function voteStatus(Contestant $contestant)
    {
        if (!auth()->check()) {
            return response()->json([
                'can_vote' => false,
                'message' => 'Please login to vote',
            ]);
        }

        $userId      = auth()->id();
        $competition = $contestant->competition;

        $hasPremium = VoteOrder::where('user_id', $userId)
            ->where('competition_id', $competition->id)
            ->where('order_type', 'premium_subscription')
            ->where('payment_status', 'completed')
            ->where('subscription_starts_at', '<=', now())
            ->where('subscription_expires_at', '>=', now())
            ->exists();

        $dailyVoteCount = Vote::where('user_id', $userId)
            ->where('competition_id', $competition->id)
            ->whereDate('vote_date', now()->toDateString())
            ->count();

        if ($hasPremium) {
            $hasVotedToday = $dailyVoteCount >= 10;
            $canVote       = !$hasVotedToday;
        } else {
            $hasVotedToday = Vote::where('user_id', $userId)
                ->where('contestant_id', $contestant->id)
                ->whereDate('vote_date', now()->toDateString())
                ->exists();
            $canVote = !$hasVotedToday;
        }

        return response()->json([
            'can_vote'        => $canVote,
            'has_voted_today' => $hasVotedToday,
            'vote_count'      => $contestant->total_votes,
            'is_premium'      => $hasPremium,
            'daily_votes_used'=> $dailyVoteCount,
            'daily_vote_limit'=> $hasPremium ? 10 : 1,
        ]);
    }
}
