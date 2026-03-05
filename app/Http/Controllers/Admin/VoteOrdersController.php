<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VoteOrder;
use App\Models\Competition;
use Illuminate\Http\Request;

class VoteOrdersController extends Controller
{
    public function index(Request $request)
    {
        $query = VoteOrder::with(['user', 'contestant', 'competition', 'package'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('type')) {
            $query->where('order_type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }

        if ($request->filled('competition_id')) {
            $query->where('competition_id', $request->competition_id);
        }

        $orders = $query->paginate(25)->withQueryString();

        $totalRevenue = VoteOrder::where('payment_status', 'completed')->sum('amount');
        $boostRevenue = VoteOrder::where('payment_status', 'completed')->where('order_type', 'vote_boost')->sum('amount');
        $premiumRevenue = VoteOrder::where('payment_status', 'completed')->where('order_type', 'premium_subscription')->sum('amount');

        $competitions = Competition::orderBy('name')->get();

        return view('admin.vote-orders.index', compact(
            'orders', 'totalRevenue', 'boostRevenue', 'premiumRevenue', 'competitions'
        ));
    }
}
