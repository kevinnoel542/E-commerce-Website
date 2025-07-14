<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    private $fastApiUrl;

    public function __construct()
    {
        $this->fastApiUrl = env('FASTAPI_API_URL', 'http://localhost:8000/api/v1');
    }

    /**
     * Display products page with real data from FastAPI
     */
    public function index(Request $request): View
    {
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 20);
        $categoryId = $request->get('category_id');
        $search = $request->get('search');
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');
        $inStockOnly = $request->get('in_stock_only', false);

        try {
            $params = [
                'page' => $page,
                'per_page' => $perPage,
                'active_only' => true
            ];

            if ($categoryId) {
                $params['category_id'] = $categoryId;
            }

            $response = Http::timeout(30)->get("{$this->fastApiUrl}/products/", $params);

            if ($response->successful()) {
                $data = $response->json();
                $products = $data['products'] ?? [];
                $rawPagination = $data['pagination'] ?? [];
                $categories = $data['categories'] ?? [];

                // Ensure pagination has all required keys
                $pagination = [
                    'total' => $rawPagination['total'] ?? count($products),
                    'page' => $rawPagination['page'] ?? 1,
                    'per_page' => $rawPagination['per_page'] ?? 20,
                    'total_pages' => $rawPagination['total_pages'] ?? 1
                ];

                // Transform images array to image_url for frontend compatibility
                $products = array_map(function ($product) {
                    if (isset($product['images']) && is_array($product['images']) && count($product['images']) > 0) {
                        // Use real images from FastAPI backend
                        $product['image_url'] = $product['images'][0];
                    } else {
                        // Add placeholder image if no image exists
                        $product['image_url'] = $this->getPlaceholderImage($product['name'] ?? 'Product');
                    }
                    return $product;
                }, $products);
            } else {
                $products = [];
                $pagination = [
                    'total' => 0,
                    'page' => 1,
                    'per_page' => 20,
                    'total_pages' => 0
                ];
                $categories = [];
            }
        } catch (\Exception $e) {
            // Fallback to mock data if FastAPI is not available
            $products = $this->getMockProducts();
            $pagination = [
                'total' => count($products),
                'page' => 1,
                'per_page' => 20,
                'total_pages' => 1
            ];
            $categories = [];
        }

        // Get cart and wishlist for UI state
        $cart = Session::get('cart', []);
        $wishlist = Session::get('wishlist', []);

        return view('products.index', [
            'products' => $products,
            'pagination' => $pagination,
            'categories' => $categories,
            'filters' => [
                'category_id' => $categoryId,
                'search' => $search,
                'min_price' => $minPrice,
                'max_price' => $maxPrice,
                'in_stock_only' => $inStockOnly
            ],
            'cartItems' => array_keys($cart),
            'wishlistItems' => array_keys($wishlist),
            'cartCount' => count($cart),
            'wishlistCount' => count($wishlist)
        ]);
    }

    /**
     * Show product details
     */
    public function show(string $productId): View
    {
        try {
            $response = Http::timeout(30)->get("{$this->fastApiUrl}/products/{$productId}");

            if ($response->successful()) {
                $product = $response->json();

                // Transform images array to image_url for frontend compatibility
                if (isset($product['images']) && is_array($product['images']) && count($product['images']) > 0) {
                    // Use real images from FastAPI backend
                    $product['image_url'] = $product['images'][0];
                } else {
                    // Add placeholder image if no image exists
                    $product['image_url'] = $this->getPlaceholderImage($product['name'] ?? 'Product');
                }
            } else {
                abort(404, 'Product not found');
            }
        } catch (\Exception $e) {
            // Fallback to mock data if FastAPI is not available
            $mockProducts = $this->getMockProducts();
            $product = collect($mockProducts)->firstWhere('id', $productId);

            if (!$product) {
                abort(404, 'Product not found');
            }
        }

        // Get cart and wishlist for UI state
        $cart = Session::get('cart', []);
        $wishlist = Session::get('wishlist', []);

        return view('products.show', [
            'product' => $product,
            'inCart' => isset($cart[$productId]),
            'inWishlist' => isset($wishlist[$productId]),
            'cartCount' => count($cart),
            'wishlistCount' => count($wishlist)
        ]);
    }

    /**
     * Search products
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 20);

        if (empty($query)) {
            return response()->json([
                'success' => false,
                'message' => 'Search query is required'
            ], 400);
        }

        try {
            $response = Http::timeout(30)->get("{$this->fastApiUrl}/products/search", [
                'q' => $query,
                'page' => $page,
                'per_page' => $perPage
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Search failed'
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Search failed. Please try again.'
            ], 500);
        }
    }

    /**
     * Get product categories
     */
    public function categories(): JsonResponse
    {
        try {
            $response = Http::timeout(30)->get("{$this->fastApiUrl}/products/categories/");

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to load categories'
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load categories'
            ], 500);
        }
    }

    /**
     * Get featured products
     */
    public function featured(): JsonResponse
    {
        try {
            $response = Http::timeout(30)->get("{$this->fastApiUrl}/products/", [
                'featured_only' => true,
                'per_page' => 8
            ]);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to load featured products'
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load featured products'
            ], 500);
        }
    }

    /**
     * Get product recommendations
     */
    public function recommendations(string $productId): JsonResponse
    {
        try {
            // For now, just get random products from the same category
            $productResponse = Http::timeout(30)->get("{$this->fastApiUrl}/products/{$productId}");

            if (!$productResponse->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            $product = $productResponse->json();
            $categoryId = $product['category_id'] ?? null;

            $params = [
                'per_page' => 4,
                'active_only' => true
            ];

            if ($categoryId) {
                $params['category_id'] = $categoryId;
            }

            $response = Http::timeout(30)->get("{$this->fastApiUrl}/products/", $params);

            if ($response->successful()) {
                $data = $response->json();
                // Filter out the current product
                $recommendations = array_filter($data['products'] ?? [], function ($p) use ($productId) {
                    return $p['id'] !== $productId;
                });

                return response()->json([
                    'success' => true,
                    'data' => array_values($recommendations)
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to load recommendations'
                ], $response->status());
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load recommendations'
            ], 500);
        }
    }

    /**
     * Get mock products data when FastAPI is not available
     */
    private function getMockProducts(): array
    {
        return [
            [
                'id' => '1',
                'name' => 'Sample Smartphone',
                'description' => 'High-quality smartphone with advanced features',
                'price' => 599.99,
                'stock_quantity' => 25,
                'category' => 'Electronics',
                'brand' => 'TechBrand',
                'sku' => 'PHONE-001',
                'image_url' => 'https://via.placeholder.com/400x400/3B82F6/FFFFFF?text=Smartphone',
                'images' => ['https://via.placeholder.com/400x400/3B82F6/FFFFFF?text=Smartphone'],
                'is_active' => true,
                'created_at' => now()->toISOString()
            ],
            [
                'id' => '2',
                'name' => 'Cotton T-Shirt',
                'description' => 'Comfortable cotton t-shirt in various colors',
                'price' => 29.99,
                'stock_quantity' => 100,
                'category' => 'Clothing',
                'brand' => 'FashionCo',
                'sku' => 'SHIRT-001',
                'image_url' => 'https://via.placeholder.com/400x400/10B981/FFFFFF?text=T-Shirt',
                'images' => ['https://via.placeholder.com/400x400/10B981/FFFFFF?text=T-Shirt'],
                'is_active' => true,
                'created_at' => now()->toISOString()
            ],
            [
                'id' => '3',
                'name' => 'Programming Book',
                'description' => 'Learn programming with this comprehensive guide',
                'price' => 10.00,
                'stock_quantity' => 30,
                'category' => 'Books',
                'brand' => 'BookCorp',
                'sku' => 'BOOK-001',
                'image_url' => 'https://via.placeholder.com/400x400/8B5CF6/FFFFFF?text=Book',
                'images' => ['https://via.placeholder.com/400x400/8B5CF6/FFFFFF?text=Book'],
                'is_active' => true,
                'created_at' => now()->toISOString()
            ],
            [
                'id' => '4',
                'name' => 'New Item',
                'description' => 'This is a test create',
                'price' => 1000.00,
                'stock_quantity' => 5,
                'category' => 'Electronics',
                'brand' => 'TestBrand',
                'sku' => 'TEST-001',
                'image_url' => 'https://via.placeholder.com/400x400/EF4444/FFFFFF?text=New+Item',
                'images' => ['https://via.placeholder.com/400x400/EF4444/FFFFFF?text=New+Item'],
                'is_active' => true,
                'created_at' => now()->toISOString()
            ],
            [
                'id' => '5',
                'name' => 'Wireless Headphones',
                'description' => 'High-quality wireless headphones with noise cancellation',
                'price' => 199.99,
                'stock_quantity' => 15,
                'category' => 'Electronics',
                'brand' => 'AudioTech',
                'sku' => 'HEAD-001',
                'image_url' => 'https://via.placeholder.com/400x400/6366F1/FFFFFF?text=Headphones',
                'images' => ['https://via.placeholder.com/400x400/6366F1/FFFFFF?text=Headphones'],
                'is_active' => true,
                'created_at' => now()->toISOString()
            ],
            [
                'id' => '6',
                'name' => 'Gaming Mouse',
                'description' => 'Professional gaming mouse with RGB lighting',
                'price' => 79.99,
                'stock_quantity' => 25,
                'category' => 'Electronics',
                'brand' => 'GameGear',
                'sku' => 'MOUSE-001',
                'image_url' => 'https://via.placeholder.com/400x400/EC4899/FFFFFF?text=Mouse',
                'images' => ['https://via.placeholder.com/400x400/EC4899/FFFFFF?text=Mouse'],
                'is_active' => true,
                'created_at' => now()->toISOString()
            ]
        ];
    }

    /**
     * Get placeholder image URL based on product name
     */
    private function getPlaceholderImage(string $productName): string
    {
        $colors = [
            'smartphone' => '3B82F6',
            'phone' => '3B82F6',
            'shirt' => '10B981',
            'clothing' => '10B981',
            'book' => '8B5CF6',
            'headphone' => '6366F1',
            'mouse' => 'EC4899',
            'mug' => 'F97316',
            'stand' => '06B6D4',
            'laptop' => 'EF4444'
        ];

        $productLower = strtolower($productName);
        $color = 'F59E0B'; // Default orange color

        foreach ($colors as $keyword => $hexColor) {
            if (strpos($productLower, $keyword) !== false) {
                $color = $hexColor;
                break;
            }
        }

        $text = urlencode(substr($productName, 0, 10));
        return "https://via.placeholder.com/400x400/{$color}/FFFFFF?text={$text}";
    }
}
