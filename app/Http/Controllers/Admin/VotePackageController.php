<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VotePackage;
use Illuminate\Http\Request;

class VotePackageController extends Controller
{
    public function index()
    {
        $packages = VotePackage::orderBy('sort_order')->get();
        return view('admin.vote-packages.index', compact('packages'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'votes_count' => 'required|integer|min:1',
            'price'       => 'required|numeric|min:0',
            'currency'    => 'required|string|max:10',
            'description' => 'nullable|string|max:500',
            'is_popular'  => 'boolean',
            'is_active'   => 'boolean',
            'sort_order'  => 'integer|min:0',
        ]);

        $data['is_popular'] = $request->boolean('is_popular');
        $data['is_active']  = $request->boolean('is_active');

        VotePackage::create($data);

        return redirect()->route('admin.vote-packages.index')->with('success', 'Package created.');
    }

    public function update(Request $request, VotePackage $votePackage)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:100',
            'votes_count' => 'required|integer|min:1',
            'price'       => 'required|numeric|min:0',
            'currency'    => 'required|string|max:10',
            'description' => 'nullable|string|max:500',
            'is_popular'  => 'boolean',
            'is_active'   => 'boolean',
            'sort_order'  => 'integer|min:0',
        ]);

        $data['is_popular'] = $request->boolean('is_popular');
        $data['is_active']  = $request->boolean('is_active');

        $votePackage->update($data);

        return redirect()->route('admin.vote-packages.index')->with('success', 'Package updated.');
    }

    public function destroy(VotePackage $votePackage)
    {
        $votePackage->delete();
        return redirect()->route('admin.vote-packages.index')->with('success', 'Package deleted.');
    }
}
