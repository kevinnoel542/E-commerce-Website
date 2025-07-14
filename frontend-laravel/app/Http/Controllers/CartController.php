<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    private $fastApiUrl;

    public function __construct()
    {
        $this->fastApiUrl = env('FASTAPI_API_URL', 'http://localhost:8000/api/v1');
    }

    /**
     * Display the cart page
     */
    public function index(): View
    {
        $cart = Session::get('cart', []);
        $cartItems = [];
        $total = 0;

        // Get product details for each cart item
        foreach ($cart as $productId => $item) {
            try {
                $response = Http::timeout(10)->get("{$this->fastApiUrl}/products/{$productId}");
                
                if ($response->successful()) {
                    $product = $response->json();
                    $cartItems[] = [
                        'product' => $product,
                        'quantity' => $item['quantity'],
                        'subtotal' => $product['price'] * $item['quantity']
                    ];
                    $total += $product['price'] * $item['quantity'];
                }
            } catch (\Exception $e) {
                // Skip items that can't be loaded
                continue;
            }
        }

        return view('cart.index', [
            'cartItems' => $cartItems,
            'total' => $total,
            'cartCount' => count($cart)
        ]);
    }

    /**
     * Add item to cart
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|string',
            'quantity' => 'integer|min:1|max:10'
        ]);

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);

        // Get product details from FastAPI
        try {
            $response = Http::timeout(10)->get("{$this->fastApiUrl}/products/{$productId}");
            
            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            $product = $response->json();
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load product details'
            ], 500);
        }

        // Get current cart from session
        $cart = Session::get('cart', []);

        // Add or update item in cart
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'quantity' => $quantity,
                'added_at' => now()->toISOString()
            ];
        }

        // Save cart to session
        Session::put('cart', $cart);

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully',
            'cartCount' => count($cart),
            'product' => $product
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|string',
            'quantity' => 'required|integer|min:1|max:10'
        ]);

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity');

        $cart = Session::get('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
            Session::put('cart', $cart);

            return response()->json([
                'success' => true,
                'message' => 'Cart updated successfully',
                'cartCount' => count($cart)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Item not found in cart'
        ], 404);
    }

    /**
     * Remove item from cart
     */
    public function remove(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|string'
        ]);

        $productId = $request->input('product_id');
        $cart = Session::get('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            Session::put('cart', $cart);

            return response()->json([
                'success' => true,
                'message' => 'Item removed from cart',
                'cartCount' => count($cart)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Item not found in cart'
        ], 404);
    }

    /**
     * Clear entire cart
     */
    public function clear(): JsonResponse
    {
        Session::forget('cart');

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully',
            'cartCount' => 0
        ]);
    }

    /**
     * Get cart count for navigation
     */
    public function count(): JsonResponse
    {
        $cart = Session::get('cart', []);
        
        return response()->json([
            'count' => count($cart)
        ]);
    }

    /**
     * Get cart summary for checkout
     */
    public function summary(): JsonResponse
    {
        $cart = Session::get('cart', []);
        $cartItems = [];
        $total = 0;

        foreach ($cart as $productId => $item) {
            try {
                $response = Http::timeout(10)->get("{$this->fastApiUrl}/products/{$productId}");
                
                if ($response->successful()) {
                    $product = $response->json();
                    $subtotal = $product['price'] * $item['quantity'];
                    
                    $cartItems[] = [
                        'product_id' => $productId,
                        'name' => $product['name'],
                        'price' => $product['price'],
                        'quantity' => $item['quantity'],
                        'subtotal' => $subtotal
                    ];
                    
                    $total += $subtotal;
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return response()->json([
            'items' => $cartItems,
            'total' => $total,
            'count' => count($cart)
        ]);
    }
}
