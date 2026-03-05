<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\VoteOrder;
use App\Services\PesapalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PesapalController extends Controller
{
    public function callback(Request $request, PesapalService $pesapal)
    {
        $trackingId = $request->query('OrderTrackingId');
        $merchantRef = $request->query('OrderMerchantReference');

        if (!$trackingId || !$merchantRef) {
            return redirect()->route('home')->with('error', 'Invalid payment callback.');
        }

        $order = VoteOrder::where('merchant_reference', $merchantRef)->first();

        if (!$order) {
            return view('pesapal.failed', ['message' => 'Order not found.']);
        }

        try {
            $status = $pesapal->getTransactionStatus($trackingId);
            $this->processPayment($order, $status, $trackingId);
        } catch (\Exception $e) {
            Log::error('Pesapal callback error', ['error' => $e->getMessage(), 'order' => $order->id]);
        }

        $order->refresh();

        if ($order->payment_status === 'completed') {
            return view('pesapal.success', compact('order'));
        }

        return view('pesapal.failed', ['message' => 'Payment was not completed. Please try again.', 'order' => $order]);
    }

    public function ipn(Request $request, PesapalService $pesapal)
    {
        $trackingId  = $request->query('OrderTrackingId');
        $merchantRef = $request->query('OrderMerchantReference');

        Log::info('Pesapal IPN received', ['tracking' => $trackingId, 'ref' => $merchantRef]);

        if ($trackingId && $merchantRef) {
            $order = VoteOrder::where('merchant_reference', $merchantRef)->first();

            if ($order) {
                try {
                    $status = $pesapal->getTransactionStatus($trackingId);
                    $this->processPayment($order, $status, $trackingId);
                } catch (\Exception $e) {
                    Log::error('Pesapal IPN processing error', ['error' => $e->getMessage()]);
                }
            }
        }

        return response()->json([
            'orderNotificationType'  => 'IPNCHANGE',
            'orderTrackingId'        => $trackingId,
            'orderMerchantReference' => $merchantRef,
            'status'                 => '200',
        ]);
    }

    // ── Shared payment processing ──────────────────────────────────

    private function processPayment(VoteOrder $order, array $status, string $trackingId): void
    {
        // Pesapal status_code 1 = COMPLETED
        $isCompleted = isset($status['status_code']) && (int) $status['status_code'] === 1;

        if (!$isCompleted) {
            $paymentStatus = match((int) ($status['status_code'] ?? 0)) {
                3       => 'failed',
                4       => 'cancelled',
                default => 'pending',
            };

            $order->update([
                'payment_status'      => $paymentStatus,
                'pesapal_tracking_id' => $trackingId,
            ]);
            return;
        }

        if ($order->votes_applied) {
            return; // already processed (idempotent)
        }

        DB::transaction(function () use ($order, $trackingId) {
            $order->update(['pesapal_tracking_id' => $trackingId]);
            $order->applyVotes();
        });
    }
}
