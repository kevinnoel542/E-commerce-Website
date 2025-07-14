<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\AuthController;

class OrderController extends Controller
{
    private $fastApiUrl;

    public function __construct()
    {
        $this->fastApiUrl = env('FASTAPI_API_URL', 'http://localhost:8000/api/v1');
    }

    /**
     * Display all orders for admin
     */
    public function adminIndex(Request $request): View
    {
        $token = AuthController::token();
        $status = $request->get('status'); // Filter by status if provided

        try {
            $url = $this->fastApiUrl . '/orders/admin';
            if ($status) {
                $url .= '?status=' . urlencode($status);
            }

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$token}",
                    'Accept' => 'application/json'
                ])
                ->get($url);

            if ($response->successful()) {
                $data = $response->json();
                $orders = $data['orders'] ?? [];
                $rawPagination = $data['pagination'] ?? [];

                // Ensure pagination has all required keys
                $pagination = [
                    'total' => $rawPagination['total'] ?? count($orders),
                    'page' => $rawPagination['page'] ?? 1,
                    'current_page' => $rawPagination['page'] ?? 1,
                    'per_page' => $rawPagination['per_page'] ?? 20,
                    'total_pages' => $rawPagination['total_pages'] ?? 1
                ];
            } else {
                $orders = [];
                $pagination = [
                    'total' => 0,
                    'page' => 1,
                    'current_page' => 1,
                    'per_page' => 20,
                    'total_pages' => 0
                ];
            }
        } catch (\Exception $e) {
            $orders = [];
            $pagination = [
                'total' => 0,
                'page' => 1,
                'current_page' => 1,
                'per_page' => 20,
                'total_pages' => 0
            ];
        }

        return view('admin.orders.index', [
            'orders' => $orders,
            'pagination' => $pagination,
            'currentStatus' => $status
        ]);
    }

    /**
     * Display user's orders
     */
    public function index(Request $request): View
    {
        $user = AuthController::user();
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 20);

        try {
            $token = Session::get('jwt_token');
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$token}",
                    'Accept' => 'application/json'
                ])
                ->get("{$this->fastApiUrl}/orders/", [
                    'page' => $page,
                    'per_page' => $perPage
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $orders = $data['orders'] ?? [];
                $rawPagination = $data['pagination'] ?? [];

                // Ensure pagination has all required keys
                $pagination = [
                    'total' => $rawPagination['total'] ?? count($orders),
                    'page' => $rawPagination['page'] ?? 1,
                    'current_page' => $rawPagination['page'] ?? 1, // Add current_page for view compatibility
                    'per_page' => $rawPagination['per_page'] ?? 20,
                    'total_pages' => $rawPagination['total_pages'] ?? 1
                ];
            } else {
                $orders = [];
                $pagination = [
                    'total' => 0,
                    'page' => 1,
                    'current_page' => 1,
                    'per_page' => 20,
                    'total_pages' => 0
                ];
            }
        } catch (\Exception $e) {
            $orders = [];
            $pagination = [
                'total' => 0,
                'page' => 1,
                'current_page' => 1,
                'per_page' => 20,
                'total_pages' => 0
            ];
        }

        return view('orders.index', [
            'orders' => $orders,
            'pagination' => $pagination,
            'user' => $user
        ]);
    }

    /**
     * Show specific order details
     */
    public function show(string $orderId): View
    {
        $user = AuthController::user();

        try {
            $token = Session::get('jwt_token');
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$token}",
                    'Accept' => 'application/json'
                ])
                ->get("{$this->fastApiUrl}/orders/{$orderId}");

            if ($response->successful()) {
                $order = $response->json();
            } else {
                abort(404, 'Order not found');
            }
        } catch (\Exception $e) {
            abort(500, 'Failed to load order details');
        }

        return view('orders.show', [
            'order' => $order,
            'user' => $user
        ]);
    }

    /**
     * Create order from cart
     */
    public function create(Request $request): JsonResponse
    {
        $request->validate([
            'shipping_address' => 'required|array',
            'shipping_address.full_name' => 'required|string|max:255',
            'shipping_address.phone' => 'required|string|max:20',
            'shipping_address.street' => 'required|string|max:255',
            'shipping_address.city' => 'required|string|max:100',
            'shipping_address.state' => 'required|string|max:100',
            'shipping_address.postal_code' => 'required|string|max:20',
            'shipping_address.country' => 'required|string|max:100',
            'payment_method' => 'required|string|in:stripe,bank_transfer'
        ]);

        $user = AuthController::user();
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty'
            ], 400);
        }

        // Prepare cart items for FastAPI
        $cartItems = [];
        foreach ($cart as $productId => $item) {
            $cartItems[] = [
                'product_id' => $productId,
                'quantity' => $item['quantity']
            ];
        }

        $orderData = [
            'items' => $cartItems,
            'shipping_address' => [
                'full_name' => $request->input('shipping_address.full_name'),
                'phone' => $request->input('shipping_address.phone'),
                'address_line_1' => $request->input('shipping_address.street'),
                'address_line_2' => '',
                'city' => $request->input('shipping_address.city'),
                'state' => $request->input('shipping_address.state'),
                'postal_code' => $request->input('shipping_address.postal_code'),
                'country' => $request->input('shipping_address.country', 'Tanzania')
            ],
            'notes' => $request->input('notes', '')
        ];

        try {
            $token = Session::get('jwt_token');
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$token}",
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->post("{$this->fastApiUrl}/orders/", $orderData);

            if ($response->successful()) {
                $order = $response->json();

                // Clear cart after successful order
                Session::forget('cart');

                return response()->json([
                    'success' => true,
                    'message' => 'Order created successfully',
                    'order' => $order,
                    'cartCount' => 0
                ]);
            } else {
                $error = $response->json();
                return response()->json([
                    'success' => false,
                    'message' => $error['detail'] ?? 'Failed to create order'
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order. Please try again.'
            ], 500);
        }
    }

    /**
     * Get cart summary for checkout
     */
    public function cartSummary(): JsonResponse
    {
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty'
            ], 400);
        }

        // Prepare cart items for FastAPI
        $cartItems = [];
        foreach ($cart as $productId => $item) {
            $cartItems[] = [
                'product_id' => $productId,
                'quantity' => $item['quantity']
            ];
        }

        try {
            $token = Session::get('jwt_token');
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$token}",
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->post("{$this->fastApiUrl}/orders/cart/summary", [
                    'items' => $cartItems
                ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to calculate cart summary'
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to calculate cart summary'
            ], 500);
        }
    }

    /**
     * Process payment for order
     */
    public function payment(Request $request, string $orderId): JsonResponse
    {
        $request->validate([
            'payment_method' => 'required|string|in:credit_card,paypal,bank_transfer',
            'payment_details' => 'required|array'
        ]);

        try {
            $token = Session::get('jwt_token');
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$token}",
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->post("{$this->fastApiUrl}/orders/{$orderId}/payment", [
                    'payment_method' => $request->input('payment_method'),
                    'payment_details' => $request->input('payment_details')
                ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Payment processed successfully',
                    'data' => $response->json()
                ]);
            } else {
                $error = $response->json();
                return response()->json([
                    'success' => false,
                    'message' => $error['detail'] ?? 'Payment failed'
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed. Please try again.'
            ], 500);
        }
    }
}
