<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Contestant;
use App\Models\VoteOrder;
use App\Models\VotePackage;
use App\Services\PesapalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoteBoostController extends Controller
{
    public function show(Contestant $contestant)
    {
        $packages    = VotePackage::active()->get();
        $competition = $contestant->competition;
        $pricePerVote = (int) env('VOTE_PRICE_PER_UNIT', 1100);

        return view('boost.show', compact('contestant', 'competition', 'packages', 'pricePerVote'));
    }

    public function initiate(Request $request, Contestant $contestant, PesapalService $pesapal)
    {
        $request->validate([
            'package_id'   => 'nullable|exists:vote_packages,id',
            'custom_votes' => 'nullable|integer|min:1|max:100000',
        ]);

        $competition  = $contestant->competition;
        $pricePerVote = (int) env('VOTE_PRICE_PER_UNIT', 1100);

        // Determine votes count & amount
        if ($request->filled('package_id')) {
            $package    = VotePackage::findOrFail($request->package_id);
            $votesCount = $package->votes_count;
            $amount     = $package->price;
            $packageId  = $package->id;
        } else {
            $votesCount = (int) $request->custom_votes;
            $amount     = $votesCount * $pricePerVote;
            $packageId  = null;
        }

        $user = auth()->user();
        $ref  = VoteOrder::generateMerchantRef();

        $order = VoteOrder::create([
            'user_id'         => $user->id,
            'contestant_id'   => $contestant->id,
            'competition_id'  => $competition->id,
            'package_id'      => $packageId,
            'order_type'      => 'vote_boost',
            'votes_count'     => $votesCount,
            'amount'          => $amount,
            'currency'        => 'UGX',
            'price_per_vote'  => $pricePerVote,
            'merchant_reference' => $ref,
            'payment_status'  => 'pending',
            'ip_address'      => $request->ip(),
        ]);

        try {
            $result = $pesapal->submitOrder([
                'merchant_reference' => $ref,
                'amount'             => $amount,
                'currency'           => 'UGX',
                'description'        => "Vote Boost: {$votesCount} votes for {$contestant->full_name}",
                'email'              => $user->email ?? '',
                'first_name'         => explode(' ', $user->name)[0] ?? '',
                'last_name'          => explode(' ', $user->name)[1] ?? '',
            ]);

            $order->update(['pesapal_tracking_id' => $result['order_tracking_id']]);

            return redirect()->away($result['redirect_url']);
        } catch (\Exception $e) {
            $order->update(['payment_status' => 'failed']);
            return redirect()->route('boost.show', $contestant)
                ->with('error', 'Payment initiation failed. Please try again.');
        }
    }
}
