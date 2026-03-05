<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CompetitionController;
use App\Http\Controllers\Admin\ContestantController;
use App\Http\Controllers\Admin\VotesController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Public\VotingController;
use App\Models\Competition;
use App\Models\Vote;
use App\Models\User;

// Home page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Authentication Routes
Route::prefix('auth')->group(function () {
    Route::get('/{provider}/redirect', [SocialAuthController::class, 'redirect'])
        ->name('social.redirect')
        ->where('provider', 'google|facebook|twitter-oauth-2');

    Route::get('/{provider}/callback', [SocialAuthController::class, 'callback'])
        ->name('social.callback')
        ->where('provider', 'google|facebook|twitter-oauth-2');

    Route::post('/logout', [SocialAuthController::class, 'logout'])
        ->name('logout')
        ->middleware('auth');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login')->middleware('guest');

// Admin email/password login
Route::get('/admin/login', function () {
    return view('auth.admin-login');
})->name('admin.login')->middleware('guest');

Route::post('/admin/login', function (\Illuminate\Http\Request $request) {
    $credentials = $request->validate([
        'email'    => 'required|email',
        'password' => 'required',
    ]);

    if (\Illuminate\Support\Facades\Auth::attempt($credentials, $request->boolean('remember'))) {
        $user = \Illuminate\Support\Facades\Auth::user();
        if (!in_array($user->role, ['admin', 'super_admin'])) {
            \Illuminate\Support\Facades\Auth::logout();
            return back()->withErrors(['email' => 'You do not have admin access.']);
        }
        $request->session()->regenerate();
        return redirect()->route('admin.dashboard');
    }

    return back()->withErrors(['email' => 'Invalid email or password.'])->onlyInput('email');
})->name('admin.login.post')->middleware('guest');

// Protected User Routes
Route::middleware(['auth'])->group(function () {
    // Voting — rate limited: 10 vote attempts per minute per user
    Route::post('/vote/{contestant}', [VotingController::class, 'vote'])->name('vote')->middleware('throttle:10,1');
    Route::get('/vote-status/{contestant}', [VotingController::class, 'voteStatus'])->name('vote.status');
});

// Admin Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Competition Management
    Route::resource('competitions', CompetitionController::class);
    Route::post('/competitions/{competition}/toggle-voting', [CompetitionController::class, 'toggleVoting'])
        ->name('competitions.toggle-voting');

    // Contestant Management
    Route::resource('contestants', ContestantController::class);

    // Votes Management
    Route::get('/votes', [VotesController::class, 'index'])->name('votes.index');
    Route::post('/votes/{vote}/status', [VotesController::class, 'updateStatus'])->name('votes.status');
    Route::delete('/votes/{vote}', [VotesController::class, 'destroy'])->name('votes.destroy');

    // Users Management
    Route::get('/users', [UsersController::class, 'index'])->name('users.index');
    Route::post('/users/{user}/toggle-active', [UsersController::class, 'toggleActive'])->name('users.toggle-active');
    Route::post('/users/{user}/role', [UsersController::class, 'updateRole'])->name('users.role');
});

// Public - Competitions listing
Route::get('/competitions', function () {
    $competitions = Competition::withCount(['contestants' => fn($q) => $q->where('status', 'active')])
        ->latest()->paginate(12);
    return view('public.competitions.index', compact('competitions'));
})->name('competitions.index');

// Public - Competition detail & voting page (by slug)
Route::get('/competitions/{slug}', function ($slug) {
    $competition = Competition::where('slug', $slug)->firstOrFail();
    // Show all contestants (not just active) so results are visible when voting is closed
    $contestants = $competition->contestants()
        ->with(['parish.region'])
        ->orderBy('total_votes', 'desc')
        ->get();
    return view('public.competitions.show', compact('competition', 'contestants'));
})->name('competitions.show');

// Public - Leaderboard
Route::get('/leaderboard', function () {
    $competitions = Competition::where('status', 'active')->with(['contestants' => function($q) {
        $q->where('status', 'active')->orderBy('total_votes', 'desc')->limit(10)->with('parish');
    }])->get();
    return view('public.leaderboard', compact('competitions'));
})->name('leaderboard');

// Public - Transparency/Trust page
Route::get('/transparency', function () {
    $stats = [
        'total_votes' => Vote::where('status', 'valid')->count(),
        'total_voters' => User::where('role', 'user')->count(),
        'suspicious_votes' => Vote::where('status', 'suspicious')->count(),
        'active_competitions' => Competition::where('status', 'active')->count(),
    ];
    $dailyVotes = Vote::where('status', 'valid')
        ->where('created_at', '>=', now()->subDays(30))
        ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
        ->groupBy('date')->orderBy('date')->get();
    return view('public.transparency', compact('stats', 'dailyVotes'));
})->name('transparency');

// Public - Archive (past competitions)
Route::get('/archive', function () {
    $competitions = Competition::whereIn('status', ['completed', 'archived'])->latest()->paginate(12);
    return view('public.archive', compact('competitions'));
})->name('archive');

Route::get('/archive/{slug}', function ($slug) {
    $competition = Competition::where('slug', $slug)->firstOrFail();
    $contestants = $competition->contestants()->orderBy('total_votes', 'desc')->with('parish')->get();
    return view('public.archive-show', compact('competition', 'contestants'));
})->name('archive.show');
