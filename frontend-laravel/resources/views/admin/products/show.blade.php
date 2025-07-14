<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-xl font-bold text-gray-800">E-Commerce Admin</h1>
                    </div>
                </div>

                <!-- Breadcrumb -->
                <div class="flex items-center space-x-2 text-sm text-gray-600">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-blue-600">Dashboard</a>
                    <i class="bi bi-chevron-right"></i>
                    <a href="{{ route('admin.products.index') }}" class="hover:text-blue-600">Products</a>
                    <i class="bi bi-chevron-right"></i>
                    <span class="text-gray-900">Product Details</span>
                </div>

                <!-- User Menu -->
                <div class="flex items-center">
                    <span class="text-sm text-gray-600 mr-2">Admin</span>
                    <span class="bg-red-500 text-white text-xs px-2 py-1 rounded mr-4">Admin</span>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="flex justify-between items-start mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Sample Smartphone</h1>
                <p class="text-gray-600">Product ID: #PRD001</p>
            </div>
            <div class="flex space-x-3">
                <a href="{{ route('admin.products.edit', 1) }}" 
                   class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="bi bi-pencil me-2"></i>Edit Product
                </a>
                <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    <i class="bi bi-trash me-2"></i>Delete Product
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Product Image -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="aspect-square bg-gray-100 rounded-lg mb-4 flex items-center justify-center">
                    <img src="https://via.placeholder.com/400x400/f3f4f6/9ca3af?text=Product+Image" 
                         alt="Sample Smartphone" 
                         class="max-w-full max-h-full object-cover rounded-lg">
                </div>
                
                <!-- Image Actions -->
                <div class="flex space-x-2">
                    <button class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="bi bi-cloud-upload me-2"></i>Change Image
                    </button>
                    <button class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        <i class="bi bi-download"></i>
                    </button>
                </div>
            </div>

            <!-- Product Information -->
            <div class="space-y-6">
                <!-- Basic Information -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Product Name</label>
                            <p class="text-gray-900 font-medium">Sample Smartphone</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Description</label>
                            <p class="text-gray-900">High-quality smartphone with advanced features including 5G connectivity, triple camera system, and long-lasting battery life.</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Category</label>
                                <span class="inline-flex px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">Electronics</span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Brand</label>
                                <p class="text-gray-900">TechBrand</p>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-500">Status</label>
                            <span class="inline-flex px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Active</span>
                        </div>
                    </div>
                </div>

                <!-- Pricing & Inventory -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Pricing & Inventory</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Price</label>
                            <p class="text-2xl font-bold text-gray-900">$599.99</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Compare Price</label>
                            <p class="text-lg text-gray-500 line-through">$699.99</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Stock Quantity</label>
                            <p class="text-gray-900 font-medium">25 units</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">SKU</label>
                            <p class="text-gray-900">SP-001-BLK</p>
                        </div>
                    </div>
                </div>

                <!-- Additional Details -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Details</h3>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Weight</label>
                            <p class="text-gray-900">0.18 kg</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Featured</label>
                            <span class="inline-flex px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded-full">Yes</span>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Created</label>
                            <p class="text-gray-900">Dec 15, 2024</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-500">Last Updated</label>
                            <p class="text-gray-900">Dec 15, 2024</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Analytics -->
        <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-lg">
                        <i class="bi bi-eye text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Views</p>
                        <p class="text-2xl font-bold text-gray-900">1,234</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-lg">
                        <i class="bi bi-cart text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Total Sales</p>
                        <p class="text-2xl font-bold text-gray-900">89</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 p-3 rounded-lg">
                        <i class="bi bi-star text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-500">Average Rating</p>
                        <p class="text-2xl font-bold text-gray-900">4.5</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="mt-8 bg-white rounded-lg shadow-sm">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recent Activity</h3>
                
                <div class="space-y-4">
                    <div class="flex items-center space-x-3">
                        <div class="bg-green-100 p-2 rounded-full">
                            <i class="bi bi-cart text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-900">Product purchased by John Doe</p>
                            <p class="text-xs text-gray-500">2 hours ago</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <div class="bg-blue-100 p-2 rounded-full">
                            <i class="bi bi-pencil text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-900">Product details updated</p>
                            <p class="text-xs text-gray-500">1 day ago</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        <div class="bg-yellow-100 p-2 rounded-full">
                            <i class="bi bi-star text-yellow-600"></i>
                        </div>
                        <div>
                            <p class="text-sm text-gray-900">New review received (5 stars)</p>
                            <p class="text-xs text-gray-500">2 days ago</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
