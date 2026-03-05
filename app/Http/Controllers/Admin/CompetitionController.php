<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\AdminLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CompetitionController extends Controller
{
    public function index()
    {
        $competitions = Competition::with('settings')->latest()->paginate(20);
        return view('admin.competitions.index', compact('competitions'));
    }

    public function create()
    {
        return view('admin.competitions.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:beauty_pageant,awards,talent_show,tourism,other',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'number_of_parishes' => 'required|integer|min:1',
            'contestants_per_parish' => 'required|integer|min:1',
            'number_of_rounds' => 'required|integer|min:1',
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $validated['status'] = 'draft';

        $competition = Competition::create($validated);

        $competition->settings()->create([
            'number_of_parishes' => $validated['number_of_parishes'],
            'contestants_per_parish' => $validated['contestants_per_parish'],
            'number_of_rounds' => $validated['number_of_rounds'],
            'votes_per_user_per_day' => 1,
            'votes_per_contestant_per_day' => 1,
            'require_social_login' => true,
        ]);

        AdminLog::logCreated(auth()->user(), 'competition', $competition);

        return redirect()->route('admin.competitions.show', $competition)
            ->with('success', 'Competition created successfully!');
    }

    public function show(Competition $competition)
    {
        $competition->load(['settings', 'rounds', 'contestants']);
        return view('admin.competitions.show', compact('competition'));
    }

    public function edit(Competition $competition)
    {
        $competition->load('settings');
        return view('admin.competitions.edit', compact('competition'));
    }

    public function update(Request $request, Competition $competition)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
            'status' => 'required|in:draft,upcoming,active,completed,archived',
        ]);

        $competition->update($validated);

        // Update settings if provided
        if ($request->filled('number_of_parishes') && $competition->settings) {
            $competition->settings->update([
                'number_of_parishes' => $request->number_of_parishes,
                'contestants_per_parish' => $request->contestants_per_parish,
                'votes_per_user_per_day' => $request->votes_per_user_per_day ?? 1,
            ]);
        }

        return redirect()->route('admin.competitions.show', $competition)
            ->with('success', 'Competition updated!');
    }

    public function toggleVoting(Competition $competition)
    {
        $competition->update(['voting_enabled' => !$competition->voting_enabled]);

        $status = $competition->voting_enabled ? 'enabled' : 'paused';
        return back()->with('success', "Voting {$status} for {$competition->name}.");
    }

    public function destroy(Competition $competition)
    {
        $competition->delete();
        return redirect()->route('admin.competitions.index')
            ->with('success', 'Competition deleted!');
    }
}
