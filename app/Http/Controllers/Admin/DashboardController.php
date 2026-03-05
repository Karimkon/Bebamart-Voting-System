<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\Contestant;
use App\Models\User;
use App\Models\Vote;
use App\Models\VoteLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display admin dashboard with statistics
     */
    public function index()
    {
        // Get overview statistics
        $stats = [
            'total_competitions' => Competition::count(),
            'active_competitions' => Competition::active()->count(),
            'total_contestants' => Contestant::count(),
            'active_contestants' => Contestant::active()->count(),
            'total_users' => User::count(),
            'total_votes' => Vote::valid()->count(),
            'votes_today' => Vote::valid()->today()->count(),
            'suspicious_votes' => Vote::suspicious()->count(),
        ];

        // Get recent competitions
        $recent_competitions = Competition::latest()
            ->take(5)
            ->get();

        // Get top voted contestants
        $top_contestants = Contestant::active()
            ->topVoted()
            ->take(10)
            ->with(['competition', 'parish'])
            ->get();

        // Get voting activity (last 7 days)
        $voting_activity = Vote::valid()
            ->where('voted_at', '>=', now()->subDays(7))
            ->select(DB::raw('DATE(voted_at) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->get();

        // Get recent suspicious activity
        $suspicious_activity = Vote::suspicious()
            ->with(['user', 'contestant'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recent_competitions',
            'top_contestants',
            'voting_activity',
            'suspicious_activity'
        ));
    }
}
