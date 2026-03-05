<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vote;
use App\Models\Competition;
use App\Models\Contestant;
use Illuminate\Http\Request;

class VotesController extends Controller
{
    public function index(Request $request)
    {
        $query = Vote::with(['user', 'contestant', 'competition'])
            ->latest('voted_at');

        if ($request->filled('competition_id')) {
            $query->where('competition_id', $request->competition_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', fn($q) => $q->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%"));
        }

        $votes = $query->paginate(50)->withQueryString();
        $competitions = Competition::orderBy('name')->get(['id', 'name']);

        $stats = [
            'total'      => Vote::count(),
            'valid'      => Vote::where('status', 'valid')->count(),
            'suspicious' => Vote::where('status', 'suspicious')->count(),
            'flagged'    => Vote::where('status', 'flagged')->count(),
        ];

        return view('admin.votes.index', compact('votes', 'competitions', 'stats'));
    }

    public function updateStatus(Request $request, Vote $vote)
    {
        $request->validate(['status' => 'required|in:valid,suspicious,flagged,rejected']);
        $vote->update(['status' => $request->status]);

        // Recalculate contestant total_votes from valid votes only
        $total = Vote::where('contestant_id', $vote->contestant_id)
            ->where('status', 'valid')->count();
        $vote->contestant->update(['total_votes' => $total]);

        return back()->with('success', 'Vote status updated.');
    }

    public function destroy(Vote $vote)
    {
        $contestantId = $vote->contestant_id;
        $vote->delete();

        // Recalculate
        $total = Vote::where('contestant_id', $contestantId)->where('status', 'valid')->count();
        Contestant::find($contestantId)?->update(['total_votes' => $total]);

        return back()->with('success', 'Vote deleted.');
    }
}
