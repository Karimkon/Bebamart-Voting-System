<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\AdminLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
            'number_of_counties' => 'required|integer|min:1',
            'contestants_per_county' => 'required|integer|min:1',
            'number_of_rounds' => 'required|integer|min:1',
            'banner_image' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('banner_image')) {
            $path = $request->file('banner_image')->store('competitions', 'public');
            $validated['banner_image'] = 'storage/' . $path;
        }

        $validated['slug'] = Str::slug($validated['name']);
        $validated['status'] = 'draft';

        $competition = Competition::create($validated);

        $competition->settings()->create([
            'number_of_counties' => $validated['number_of_counties'],
            'contestants_per_county' => $validated['contestants_per_county'],
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
            'banner_image' => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('banner_image')) {
            if ($competition->banner_image) {
                Storage::disk('public')->delete(str_replace('storage/', '', $competition->banner_image));
            }
            $path = $request->file('banner_image')->store('competitions', 'public');
            $validated['banner_image'] = 'storage/' . $path;
        }

        $competition->update($validated);

        // Update settings if provided
        if ($request->filled('number_of_counties') && $competition->settings) {
            $competition->settings->update([
                'number_of_counties' => $request->number_of_counties,
                'contestants_per_county' => $request->contestants_per_county,
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
