<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class WishlistController extends Controller
{
    private $fastApiUrl;

    public function __construct()
    {
        $this->fastApiUrl = env('FASTAPI_API_URL', 'http://localhost:8000/api/v1');
    }

    /**
     * Display the wishlist page
     */
    public function index(): View
    {
        $wishlist = Session::get('wishlist', []);
        $wishlistItems = [];

        // Get product details for each wishlist item
        foreach ($wishlist as $productId => $item) {
            try {
                $response = Http::timeout(10)->get("{$this->fastApiUrl}/products/{$productId}");
                
                if ($response->successful()) {
                    $product = $response->json();
                    $wishlistItems[] = [
                        'product' => $product,
                        'added_at' => $item['added_at']
                    ];
                }
            } catch (\Exception $e) {
                // Skip items that can't be loaded
                continue;
            }
        }

        return view('wishlist.index', [
            'wishlistItems' => $wishlistItems,
            'wishlistCount' => count($wishlist)
        ]);
    }

    /**
     * Add item to wishlist
     */
    public function add(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|string'
        ]);

        $productId = $request->input('product_id');

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

        // Get current wishlist from session
        $wishlist = Session::get('wishlist', []);

        // Check if item already exists
        if (isset($wishlist[$productId])) {
            return response()->json([
                'success' => false,
                'message' => 'Product already in wishlist'
            ], 409);
        }

        // Add item to wishlist
        $wishlist[$productId] = [
            'added_at' => now()->toISOString()
        ];

        // Save wishlist to session
        Session::put('wishlist', $wishlist);

        return response()->json([
            'success' => true,
            'message' => 'Product added to wishlist successfully',
            'wishlistCount' => count($wishlist),
            'product' => $product
        ]);
    }

    /**
     * Remove item from wishlist
     */
    public function remove(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|string'
        ]);

        $productId = $request->input('product_id');
        $wishlist = Session::get('wishlist', []);

        if (isset($wishlist[$productId])) {
            unset($wishlist[$productId]);
            Session::put('wishlist', $wishlist);

            return response()->json([
                'success' => true,
                'message' => 'Item removed from wishlist',
                'wishlistCount' => count($wishlist)
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Item not found in wishlist'
        ], 404);
    }

    /**
     * Toggle item in wishlist (add if not exists, remove if exists)
     */
    public function toggle(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|string'
        ]);

        $productId = $request->input('product_id');
        $wishlist = Session::get('wishlist', []);

        if (isset($wishlist[$productId])) {
            // Remove from wishlist
            unset($wishlist[$productId]);
            Session::put('wishlist', $wishlist);

            return response()->json([
                'success' => true,
                'action' => 'removed',
                'message' => 'Item removed from wishlist',
                'wishlistCount' => count($wishlist)
            ]);
        } else {
            // Add to wishlist
            $wishlist[$productId] = [
                'added_at' => now()->toISOString()
            ];
            Session::put('wishlist', $wishlist);

            return response()->json([
                'success' => true,
                'action' => 'added',
                'message' => 'Item added to wishlist',
                'wishlistCount' => count($wishlist)
            ]);
        }
    }

    /**
     * Move item from wishlist to cart
     */
    public function moveToCart(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|string',
            'quantity' => 'integer|min:1|max:10'
        ]);

        $productId = $request->input('product_id');
        $quantity = $request->input('quantity', 1);

        $wishlist = Session::get('wishlist', []);
        $cart = Session::get('cart', []);

        // Check if item exists in wishlist
        if (!isset($wishlist[$productId])) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found in wishlist'
            ], 404);
        }

        // Add to cart
        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'quantity' => $quantity,
                'added_at' => now()->toISOString()
            ];
        }

        // Remove from wishlist
        unset($wishlist[$productId]);

        // Save both sessions
        Session::put('cart', $cart);
        Session::put('wishlist', $wishlist);

        return response()->json([
            'success' => true,
            'message' => 'Item moved to cart successfully',
            'cartCount' => count($cart),
            'wishlistCount' => count($wishlist)
        ]);
    }

    /**
     * Clear entire wishlist
     */
    public function clear(): JsonResponse
    {
        Session::forget('wishlist');

        return response()->json([
            'success' => true,
            'message' => 'Wishlist cleared successfully',
            'wishlistCount' => 0
        ]);
    }

    /**
     * Get wishlist count for navigation
     */
    public function count(): JsonResponse
    {
        $wishlist = Session::get('wishlist', []);
        
        return response()->json([
            'count' => count($wishlist)
        ]);
    }

    /**
     * Check if product is in wishlist
     */
    public function check(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|string'
        ]);

        $productId = $request->input('product_id');
        $wishlist = Session::get('wishlist', []);

        return response()->json([
            'inWishlist' => isset($wishlist[$productId])
        ]);
    }
}
