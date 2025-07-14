<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\AuthController;

class DashboardController extends Controller
{
    /**
     * Show user dashboard
     */
    public function userDashboard(): View
    {
        $user = AuthController::user();
        $token = AuthController::token();
        $fastApiUrl = 'http://127.0.0.1:8000/api/v1';

        // Initialize data arrays
        $orders = [];
        $stats = [
            'total_orders' => 0,
            'pending_orders' => 0,
            'completed_orders' => 0,
            'recent_orders' => 0
        ];

        try {
            // Call /orders API with JWT to get user's orders
            $ordersResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ])->get($fastApiUrl . '/orders');

            if ($ordersResponse->successful()) {
                $orders = $ordersResponse->json() ?? [];
                $stats['total_orders'] = count($orders);

                // Calculate order statistics
                foreach ($orders as $order) {
                    $status = $order['status'] ?? '';
                    if ($status === 'pending') {
                        $stats['pending_orders']++;
                    } elseif (in_array($status, ['completed', 'delivered'])) {
                        $stats['completed_orders']++;
                    }

                    // Count recent orders (last 30 days)
                    if (isset($order['created_at'])) {
                        $orderDate = strtotime($order['created_at']);
                        $thirtyDaysAgo = strtotime('-30 days');
                        if ($orderDate >= $thirtyDaysAgo) {
                            $stats['recent_orders']++;
                        }
                    }
                }
            }

        } catch (\Exception $e) {
            // Log error but continue with empty data
            \Log::error('User dashboard API error: ' . $e->getMessage());
        }

        return view('dashboard.user', [
            'user' => $user,
            'role' => 'user',
            'orders' => $orders,
            'stats' => $stats
        ]);
    }

    /**
     * Show admin dashboard
     */
    public function adminDashboard(): View
    {
        $user = AuthController::user();
        $token = AuthController::token();
        $fastApiUrl = 'http://127.0.0.1:8000/api/v1';

        // Initialize data arrays
        $products = [];
        $orders = [];
        $payments = [];
        $stats = [
            'total_products' => 0,
            'total_orders' => 0,
            'total_payments' => 0,
            'recent_orders' => 0
        ];

        try {
            // Call /products API
            $productsResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ])->get($fastApiUrl . '/products');

            if ($productsResponse->successful()) {
                $products = $productsResponse->json() ?? [];
                $stats['total_products'] = count($products);
            }

            // Call /orders API
            $ordersResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ])->get($fastApiUrl . '/orders');

            if ($ordersResponse->successful()) {
                $orders = $ordersResponse->json() ?? [];
                $stats['total_orders'] = count($orders);

                // Count recent orders (last 7 days)
                $recentDate = now()->subDays(7)->toISOString();
                $stats['recent_orders'] = collect($orders)->filter(function ($order) use ($recentDate) {
                    return isset($order['created_at']) && $order['created_at'] >= $recentDate;
                })->count();
            }

            // Call /payments API
            $paymentsResponse = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json'
            ])->get($fastApiUrl . '/payments');

            if ($paymentsResponse->successful()) {
                $payments = $paymentsResponse->json() ?? [];
                $stats['total_payments'] = count($payments);
            }
        } catch (\Exception $e) {
            // Log error but continue with empty data
            \Log::error('Admin dashboard API error: ' . $e->getMessage());
        }

        return view('dashboard.admin', [
            'user' => $user,
            'role' => 'admin',
            'products' => $products,
            'orders' => $orders,
            'payments' => $payments,
            'stats' => $stats
        ]);
    }
}
