<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\AuthController;

class PaymentController extends Controller
{
    private string $fastApiUrl;

    public function __construct()
    {
        $this->fastApiUrl = 'http://127.0.0.1:8000/api/v1';
    }

    /**
     * Create a payment link for an order
     */
    public function createPaymentLink(Request $request): JsonResponse
    {
        $request->validate([
            'order_id' => 'required|string',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'string|in:tzs,usd',
            'description' => 'string|max:255'
        ]);

        $token = AuthController::token();
        
        try {
            // Call POST /payments/create-link API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ])->post($this->fastApiUrl . '/payments/create-link', [
                'order_id' => $request->order_id,
                'amount' => (float) $request->amount,
                'currency' => $request->currency ?? 'tzs',
                'description' => $request->description
            ]);

            if ($response->successful()) {
                $paymentData = $response->json();
                
                return response()->json([
                    'success' => true,
                    'payment_link' => $paymentData['payment_link'],
                    'session_id' => $paymentData['session_id'],
                    'order_id' => $paymentData['order_id'],
                    'amount' => $paymentData['amount'],
                    'currency' => $paymentData['currency'],
                    'expires_at' => $paymentData['expires_at'] ?? null,
                    'message' => 'Payment link created successfully'
                ]);
                
            } else {
                $error = $response->json();
                $errorMessage = $error['detail'] ?? 'Failed to create payment link';
                
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], $response->status());
            }
            
        } catch (\Exception $e) {
            \Log::error('Payment link creation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Network error: Unable to create payment link'
            ], 500);
        }
    }

    /**
     * Handle payment success callback
     */
    public function paymentSuccess(Request $request)
    {
        $sessionId = $request->get('session_id');
        $orderId = $request->get('order_id');
        
        return view('payment.success', [
            'session_id' => $sessionId,
            'order_id' => $orderId,
            'message' => 'Payment completed successfully!'
        ]);
    }

    /**
     * Handle payment cancellation callback
     */
    public function paymentCancel(Request $request)
    {
        $sessionId = $request->get('session_id');
        $orderId = $request->get('order_id');
        
        return view('payment.cancel', [
            'session_id' => $sessionId,
            'order_id' => $orderId,
            'message' => 'Payment was cancelled.'
        ]);
    }

    /**
     * Verify payment status
     */
    public function verifyPayment(Request $request): JsonResponse
    {
        $request->validate([
            'session_id' => 'required|string'
        ]);

        $token = AuthController::token();
        
        try {
            // Call Stripe verify endpoint
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ])->get($this->fastApiUrl . '/stripe/verify/' . $request->session_id);

            if ($response->successful()) {
                $verificationData = $response->json();
                
                return response()->json([
                    'success' => true,
                    'payment_status' => $verificationData['payment_status'],
                    'order_id' => $verificationData['order_id'] ?? null,
                    'amount' => $verificationData['amount'] ?? null,
                    'currency' => $verificationData['currency'] ?? null,
                    'paid_at' => $verificationData['paid_at'] ?? null
                ]);
                
            } else {
                $error = $response->json();
                $errorMessage = $error['detail'] ?? 'Failed to verify payment';
                
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], $response->status());
            }
            
        } catch (\Exception $e) {
            \Log::error('Payment verification error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Network error: Unable to verify payment'
            ], 500);
        }
    }
}
