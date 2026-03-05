<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contestant;
use App\Models\Competition;
use App\Models\Region;
use App\Models\County;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ContestantController extends Controller
{
    public function index()
    {
        $contestants = Contestant::with(['competition', 'county.region'])
            ->latest()
            ->paginate(30);

        $competitions = Competition::orderBy('name')->get(['id', 'name']);

        return view('admin.contestants.index', compact('contestants', 'competitions'));
    }

    public function create()
    {
        $competitions = Competition::orderBy('name')->get(['id', 'name', 'status']);
        $regions = Region::active()->with('counties')->orderBy('name')->get();
        $selectedCompetitionId = request('competition_id');

        return view('admin.contestants.create', compact('competitions', 'regions', 'selectedCompetitionId'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'competition_id'    => 'required|exists:competitions,id',
            'full_name'         => 'required|string|max:255',
            'age'               => 'nullable|integer|min:1|max:100',
            'county_id'         => 'nullable|exists:counties,id',
            'biography'         => 'nullable|string|max:2000',
            'talent_description'=> 'nullable|string|max:1000',
            'contestant_number' => 'nullable|string|max:20|unique:contestants,contestant_number',
            'profile_photo'     => 'nullable|image|max:4096',
            'status'            => 'required|in:active,eliminated,qualified,winner',
        ]);

        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('contestants', 'public');
            $validated['profile_photo'] = 'storage/' . $path;
        }

        if (empty($validated['contestant_number'])) {
            $num = Contestant::max(DB::raw('CAST(contestant_number AS UNSIGNED)')) ?? 0;
            do {
                $num++;
                $candidate = str_pad($num, 3, '0', STR_PAD_LEFT);
            } while (Contestant::where('contestant_number', $candidate)->exists());
            $validated['contestant_number'] = $candidate;
        }

        if (!empty($validated['county_id'])) {
            $county = County::find($validated['county_id']);
            if ($county) {
                $validated['region_id'] = $county->region_id;
            }
        }

        $contestant = Contestant::create($validated);

        return redirect()->route('admin.competitions.show', $validated['competition_id'])
            ->with('success', $contestant->full_name . ' added successfully!');
    }

    public function show(Contestant $contestant)
    {
        $contestant->load(['competition', 'county.region', 'votes' => fn($q) => $q->latest()->limit(20)]);
        return view('admin.contestants.show', compact('contestant'));
    }

    public function edit(Contestant $contestant)
    {
        $contestant->load('county.region');
        $competitions = Competition::orderBy('name')->get(['id', 'name']);
        $regions = Region::active()->with('counties')->orderBy('name')->get();

        return view('admin.contestants.edit', compact('contestant', 'competitions', 'regions'));
    }

    public function update(Request $request, Contestant $contestant)
    {
        $validated = $request->validate([
            'full_name'         => 'required|string|max:255',
            'age'               => 'nullable|integer|min:1|max:100',
            'county_id'         => 'nullable|exists:counties,id',
            'biography'         => 'nullable|string|max:2000',
            'talent_description'=> 'nullable|string|max:1000',
            'contestant_number' => 'nullable|string|max:20|unique:contestants,contestant_number,'.$contestant->id,
            'profile_photo'     => 'nullable|image|max:4096',
            'status'            => 'required|in:active,eliminated,qualified,winner',
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($contestant->profile_photo) {
                $old = str_replace('storage/', '', $contestant->profile_photo);
                Storage::disk('public')->delete($old);
            }
            $path = $request->file('profile_photo')->store('contestants', 'public');
            $validated['profile_photo'] = 'storage/' . $path;
        }

        if (!empty($validated['county_id'])) {
            $county = County::find($validated['county_id']);
            if ($county) {
                $validated['region_id'] = $county->region_id;
            }
        }

        $contestant->update($validated);

        return redirect()->route('admin.contestants.show', $contestant)
            ->with('success', 'Contestant updated successfully!');
    }

    public function destroy(Contestant $contestant)
    {
        $competitionId = $contestant->competition_id;

        if ($contestant->profile_photo) {
            $path = str_replace('storage/', '', $contestant->profile_photo);
            Storage::disk('public')->delete($path);
        }

        $contestant->delete();

        return redirect()->route('admin.competitions.show', $competitionId)
            ->with('success', 'Contestant removed.');
    }
}