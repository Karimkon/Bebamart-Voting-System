<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vote;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('votes')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(fn($q) => $q->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%"));
        }
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active' ? 1 : 0);
        }

        $users = $query->paginate(50)->withQueryString();

        $stats = [
            'total'   => User::count(),
            'active'  => User::where('is_active', 1)->count(),
            'blocked' => User::where('is_active', 0)->count(),
            'admins'  => User::whereIn('role', ['admin', 'super_admin'])->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function toggleActive(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot block yourself.']);
        }
        $user->update(['is_active' => !$user->is_active]);
        $action = $user->is_active ? 'unblocked' : 'blocked';
        return back()->with('success', $user->name . ' has been ' . $action . '.');
    }

    public function updateRole(Request $request, User $user)
    {
        $request->validate(['role' => 'required|in:user,admin,super_admin']);
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'You cannot change your own role.']);
        }
        $user->update(['role' => $request->role]);
        return back()->with('success', $user->name . "'s role updated to " . $request->role . '.');
    }
}
