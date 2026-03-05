<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Competition;
use App\Models\VoteOrder;
use App\Services\PesapalService;
use Illuminate\Http\Request;

class PremiumController extends Controller
{
    public function show(Competition $competition)
    {
        $hasPremium = false;
        if (auth()->check()) {
            $hasPremium = VoteOrder::where('user_id', auth()->id())
                ->where('competition_id', $competition->id)
                ->where('order_type', 'premium_subscription')
                ->where('payment_status', 'completed')
                ->where('subscription_starts_at', '<=', now())
                ->where('subscription_expires_at', '>=', now())
                ->exists();
        }

        $price = (int) env('PREMIUM_PRICE', 50000);

        return view('premium.show', compact('competition', 'hasPremium', 'price'));
    }

    public function initiate(Request $request, Competition $competition, PesapalService $pesapal)
    {
        // Prevent duplicate active subscription
        $existing = VoteOrder::where('user_id', auth()->id())
            ->where('competition_id', $competition->id)
            ->where('order_type', 'premium_subscription')
            ->where('payment_status', 'completed')
            ->where('subscription_expires_at', '>=', now())
            ->first();

        if ($existing) {
            return redirect()->route('competitions.show', $competition->slug)
                ->with('info', 'You already have an active Premium subscription for this competition.');
        }

        $user   = auth()->user();
        $price  = (int) env('PREMIUM_PRICE', 50000);
        $ref    = VoteOrder::generateMerchantRef();

        $order = VoteOrder::create([
            'user_id'                => $user->id,
            'competition_id'         => $competition->id,
            'contestant_id'          => null,
            'order_type'             => 'premium_subscription',
            'votes_count'            => 10,
            'amount'                 => $price,
            'currency'               => 'UGX',
            'merchant_reference'     => $ref,
            'payment_status'         => 'pending',
            'subscription_starts_at' => now(),
            'subscription_expires_at'=> $competition->end_date ?? now()->addDays(365),
            'ip_address'             => $request->ip(),
        ]);

        try {
            $result = $pesapal->submitOrder([
                'merchant_reference' => $ref,
                'amount'             => $price,
                'currency'           => 'UGX',
                'description'        => "Premium Subscription — {$competition->name}",
                'email'              => $user->email ?? '',
                'first_name'         => explode(' ', $user->name)[0] ?? '',
                'last_name'          => explode(' ', $user->name)[1] ?? '',
            ]);

            $order->update(['pesapal_tracking_id' => $result['order_tracking_id']]);

            return redirect()->away($result['redirect_url']);
        } catch (\Exception $e) {
            $order->update(['payment_status' => 'failed']);
            return redirect()->route('premium.show', $competition)
                ->with('error', 'Payment initiation failed. Please try again.');
        }
    }
}
