<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Controller;

class CheckoutController extends Controller
{
    /**
     * Show checkout page
     */
    public function index(): View|RedirectResponse
    {
        $user = AuthController::user();
        $cart = Session::get('cart', []);

        // Redirect to cart if empty
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty');
        }

        return view('checkout.index', [
            'user' => $user,
            'cart' => $cart
        ]);
    }

    /**
     * Process checkout and create order
     */
    public function process(Request $request)
    {
        // Validate the checkout form data according to database schema
        $request->validate([
            // Shipping address validation (matches ShippingAddress model)
            'shipping_address.full_name' => 'required|string|max:255',
            'shipping_address.phone' => 'required|string|max:20',
            'shipping_address.street' => 'required|string|max:255',
            'shipping_address.city' => 'required|string|max:100',
            'shipping_address.state' => 'required|string|max:100',
            'shipping_address.postal_code' => 'required|string|max:20',
            'shipping_address.country' => 'required|string|max:100',

            // Payment and order details
            'payment_method' => 'required|string|in:stripe,bank_transfer',
            'notes' => 'nullable|string|max:1000'
        ]);

        $user = AuthController::user();
        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty'
            ], 400);
        }

        // Prepare cart items for FastAPI (matches OrderItemCreate)
        $cartItems = [];
        foreach ($cart as $productId => $item) {
            $cartItems[] = [
                'product_id' => $productId,
                'quantity' => (int) $item['quantity']
            ];
        }

        // Prepare order data (matches OrderCreate model)
        $orderData = [
            'items' => $cartItems,
            'shipping_address' => [
                'full_name' => $request->input('shipping_address.full_name'),
                'phone' => $request->input('shipping_address.phone'),
                'address_line_1' => $request->input('shipping_address.street'), // Map street to address_line_1 for backend
                'address_line_2' => '', // Optional field
                'city' => $request->input('shipping_address.city'),
                'state' => $request->input('shipping_address.state'),
                'postal_code' => $request->input('shipping_address.postal_code'),
                'country' => $request->input('shipping_address.country', 'Tanzania')
            ],
            'notes' => $request->input('notes', '')
        ];

        try {
            $token = AuthController::token();
            $fastApiUrl = 'http://127.0.0.1:8000/api/v1';

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$token}",
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->post("{$fastApiUrl}/orders/", $orderData);

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
            \Log::error('Order creation error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Network error: Unable to create order'
            ], 500);
        }
    }
}
