<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;

class AdminProductController extends Controller
{
    private string $fastApiUrl;

    public function __construct()
    {
        $this->fastApiUrl = config('fastapi.url', 'http://localhost:8000/api/v1');
    }

    /**
     * Display admin products page with real data from FastAPI
     */
    public function index(Request $request): View
    {
        $page = $request->get('page', 1);
        $perPage = $request->get('per_page', 20);
        $categoryId = $request->get('category_id');
        $search = $request->get('search');

        try {
            $token = Session::get('jwt_token');
            $params = [
                'page' => $page,
                'per_page' => $perPage,
                'active_only' => false // Admin can see all products including inactive
            ];

            if ($categoryId) {
                $params['category_id'] = $categoryId;
            }

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$token}",
                    'Accept' => 'application/json'
                ])
                ->get("{$this->fastApiUrl}/products/", $params);

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
                        // Convert FastAPI backend image URL to accessible URL
                        $imageUrl = $product['images'][0];

                        // If it's a relative path from FastAPI backend, convert to full URL
                        if (strpos($imageUrl, 'http') !== 0) {
                            // Remove leading slash if present
                            $imageUrl = ltrim($imageUrl, '/');
                            // Create full URL pointing to FastAPI backend
                            $product['image_url'] = "http://localhost:8000/{$imageUrl}";
                        } else {
                            $product['image_url'] = $imageUrl;
                        }
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

        return view('admin.products.index', [
            'products' => $products,
            'pagination' => $pagination,
            'categories' => $categories,
            'filters' => [
                'category_id' => $categoryId,
                'search' => $search
            ]
        ]);
    }

    /**
     * Show the form for creating a new product
     */
    public function create(): View
    {
        try {
            $token = Session::get('jwt_token');
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$token}",
                    'Accept' => 'application/json'
                ])
                ->get("{$this->fastApiUrl}/products/categories/");

            if ($response->successful()) {
                $categories = $response->json();
            } else {
                $categories = [];
            }
        } catch (\Exception $e) {
            $categories = [];
        }

        return view('admin.products.create', [
            'categories' => $categories
        ]);
    }

    /**
     * Upload product image to FastAPI backend
     */
    public function uploadImage(Request $request): JsonResponse
    {
        try {
            $token = Session::get('jwt_token');

            if (!$request->hasFile('image')) {
                return response()->json([
                    'success' => false,
                    'message' => 'No image file provided'
                ], 400);
            }

            $file = $request->file('image');

            // Validate image
            if (!$file->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid image file'
                ], 400);
            }

            // Create multipart form data for FastAPI
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$token}",
                    'Accept' => 'application/json'
                ])
                ->attach('file', file_get_contents($file->path()), $file->getClientOriginalName())
                ->post("{$this->fastApiUrl}/products/upload-image");

            if ($response->successful()) {
                $imageData = $response->json();
                return response()->json([
                    'success' => true,
                    'message' => 'Image uploaded successfully!',
                    'image' => $imageData
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to upload image: ' . $response->body()
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error uploading image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $token = Session::get('jwt_token');

            // Check if user is authenticated
            if (!$token) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication required. Please login again.'
                ], 401);
            }

            // Validate required fields
            $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'required|string',
                'price' => 'required|numeric|min:0',
                'stock_quantity' => 'required|integer|min:0'
            ]);

            // Handle image upload first if present
            $imageUrls = [];
            if ($request->hasFile('image')) {
                try {
                    $imageUploadResponse = $this->uploadImage($request);
                    $imageUploadData = json_decode($imageUploadResponse->getContent(), true);

                    if ($imageUploadData['success']) {
                        $imageUrls[] = $imageUploadData['image']['url'];
                    } else {
                        // Continue without image if upload fails
                        \Log::warning('Image upload failed: ' . ($imageUploadData['message'] ?? 'Unknown error'));
                    }
                } catch (\Exception $imageError) {
                    // Continue without image if upload fails
                    \Log::warning('Image upload exception: ' . $imageError->getMessage());
                }
            }

            // Prepare product data
            $productData = [
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'price' => (float) $request->input('price'),
                'category_id' => $request->input('category_id') ?: null,
                'brand' => $request->input('brand') ?: null,
                'stock_quantity' => (int) $request->input('stock_quantity', 0),
                'images' => $imageUrls
            ];

            // Log the data being sent for debugging
            \Log::info('Creating product with data:', $productData);

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$token}",
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->post("{$this->fastApiUrl}/products/", $productData);

            // Log the response for debugging
            \Log::info('FastAPI response status: ' . $response->status());
            \Log::info('FastAPI response body: ' . $response->body());

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product created successfully!',
                    'product' => $response->json()
                ]);
            } else {
                $errorMessage = 'Failed to create product';
                $responseBody = $response->body();

                // Try to parse error message from response
                try {
                    $errorData = json_decode($responseBody, true);
                    if (isset($errorData['detail'])) {
                        $errorMessage .= ': ' . $errorData['detail'];
                    } else {
                        $errorMessage .= ': ' . $responseBody;
                    }
                } catch (\Exception $e) {
                    $errorMessage .= ': ' . $responseBody;
                }

                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], $response->status());
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed: ' . implode(', ', $e->validator->errors()->all())
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Product creation error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => 'Error creating product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified product
     */
    public function show(string $id): View
    {
        try {
            $token = Session::get('jwt_token');
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$token}",
                    'Accept' => 'application/json'
                ])
                ->get("{$this->fastApiUrl}/products/{$id}");

            if ($response->successful()) {
                $product = $response->json();

                // Transform images array to image_url for frontend compatibility
                if (isset($product['images']) && is_array($product['images']) && count($product['images']) > 0) {
                    $imageUrl = $product['images'][0];

                    // If it's a relative path from FastAPI backend, convert to full URL
                    if (strpos($imageUrl, 'http') !== 0) {
                        $imageUrl = ltrim($imageUrl, '/');
                        $product['image_url'] = "http://localhost:8000/{$imageUrl}";
                    } else {
                        $product['image_url'] = $imageUrl;
                    }
                } else {
                    $product['image_url'] = $this->getPlaceholderImage($product['name'] ?? 'Product');
                }
            } else {
                abort(404, 'Product not found');
            }
        } catch (\Exception $e) {
            // Fallback to mock data if FastAPI is not available
            $mockProducts = $this->getMockProducts();
            $product = collect($mockProducts)->firstWhere('id', $id);

            if (!$product) {
                abort(404, 'Product not found');
            }
        }

        return view('admin.products.show', [
            'product' => $product
        ]);
    }

    /**
     * Show the form for editing the specified product
     */
    public function edit(string $id): View
    {
        try {
            $token = Session::get('jwt_token');

            // Get product data
            $productResponse = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$token}",
                    'Accept' => 'application/json'
                ])
                ->get("{$this->fastApiUrl}/products/{$id}");

            // Get categories
            $categoriesResponse = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$token}",
                    'Accept' => 'application/json'
                ])
                ->get("{$this->fastApiUrl}/products/categories/");

            if ($productResponse->successful()) {
                $product = $productResponse->json();

                // Transform images array to image_url for frontend compatibility
                if (isset($product['images']) && is_array($product['images']) && count($product['images']) > 0) {
                    $imageUrl = $product['images'][0];

                    // If it's a relative path from FastAPI backend, convert to full URL
                    if (strpos($imageUrl, 'http') !== 0) {
                        $imageUrl = ltrim($imageUrl, '/');
                        $product['image_url'] = "http://localhost:8000/{$imageUrl}";
                    } else {
                        $product['image_url'] = $imageUrl;
                    }
                } else {
                    $product['image_url'] = $this->getPlaceholderImage($product['name'] ?? 'Product');
                }
            } else {
                abort(404, 'Product not found');
            }

            $categories = $categoriesResponse->successful() ? $categoriesResponse->json() : [];
        } catch (\Exception $e) {
            // Fallback to mock data if FastAPI is not available
            $mockProducts = $this->getMockProducts();
            $product = collect($mockProducts)->firstWhere('id', $id);

            if (!$product) {
                abort(404, 'Product not found');
            }

            $categories = [];
        }

        return view('admin.products.edit', [
            'product' => $product,
            'categories' => $categories
        ]);
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $token = Session::get('jwt_token');

            // Prepare update data (only include fields that are set)
            $updateData = [];

            if ($request->has('name')) {
                $updateData['name'] = $request->input('name');
            }
            if ($request->has('description')) {
                $updateData['description'] = $request->input('description');
            }
            if ($request->has('price')) {
                $updateData['price'] = (float) $request->input('price');
            }
            if ($request->has('category_id')) {
                $updateData['category_id'] = $request->input('category_id');
            }
            if ($request->has('brand')) {
                $updateData['brand'] = $request->input('brand');
            }
            if ($request->has('stock_quantity')) {
                $updateData['stock_quantity'] = (int) $request->input('stock_quantity');
            }
            if ($request->has('images')) {
                $updateData['images'] = $request->input('images');
            }
            if ($request->has('is_active')) {
                $updateData['is_active'] = (bool) $request->input('is_active');
            }

            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$token}",
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->patch("{$this->fastApiUrl}/products/{$id}", $updateData);

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product updated successfully!',
                    'product' => $response->json()
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update product: ' . $response->body()
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating product: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified product
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $token = Session::get('jwt_token');
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$token}",
                    'Accept' => 'application/json'
                ])
                ->delete("{$this->fastApiUrl}/products/{$id}");

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Product deleted successfully!'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete product: ' . $response->body()
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting product: ' . $e->getMessage()
            ], 500);
        }
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
            ]
        ];
    }
}
