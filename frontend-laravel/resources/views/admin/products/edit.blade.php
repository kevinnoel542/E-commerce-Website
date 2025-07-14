<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Admin</title>
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
                    <span class="text-gray-900">Edit Product</span>
                </div>

                <!-- User Menu -->
                <div class="flex items-center">
                    <span class="text-sm text-gray-600 mr-2">Admin</span>
                    <span class="bg-red-500 text-white text-xs px-2 py-1 rounded mr-4">Admin</span>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Edit Product</h1>
            <p class="text-gray-600">Update product information</p>
        </div>

        <!-- Product Form -->
        <form class="space-y-6" x-data="{ 
            imagePreview: 'https://via.placeholder.com/400x400/f3f4f6/9ca3af?text=Current+Image',
            handleImageUpload(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imagePreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            }
        }">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Product Information -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                                <input type="text" name="name" value="Sample Smartphone" required
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Enter product name">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                                <textarea name="description" rows="4" required
                                          class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                          placeholder="Enter product description">High-quality smartphone with advanced features including 5G connectivity, triple camera system, and long-lasting battery life.</textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                                    <select name="category" required
                                            class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select Category</option>
                                        <option value="electronics" selected>Electronics</option>
                                        <option value="clothing">Clothing</option>
                                        <option value="books">Books</option>
                                        <option value="home">Home & Garden</option>
                                        <option value="sports">Sports</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                                    <input type="text" name="brand" value="TechBrand"
                                           class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           placeholder="Enter brand name">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing & Inventory -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Pricing & Inventory</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Price *</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500">$</span>
                                    <input type="number" name="price" step="0.01" value="599.99" required
                                           class="w-full border border-gray-300 rounded-md pl-8 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           placeholder="0.00">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Compare Price</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500">$</span>
                                    <input type="number" name="compare_price" step="0.01" value="699.99"
                                           class="w-full border border-gray-300 rounded-md pl-8 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           placeholder="0.00">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity *</label>
                                <input type="number" name="stock_quantity" value="25" required
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="0">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
                            <input type="text" name="sku" value="SP-001-BLK"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                   placeholder="Enter SKU">
                        </div>
                    </div>

                    <!-- Additional Details -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Additional Details</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Weight (kg)</label>
                                <input type="number" name="weight" step="0.01" value="0.18"
                                       class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="0.00">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="status"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="active" selected>Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="draft">Draft</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="featured" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700">Featured Product</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Product Image -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Product Image</h3>
                        
                        <div class="space-y-4">
                            <!-- Current Image -->
                            <div class="text-center">
                                <p class="text-sm text-gray-600 mb-2">Current Image:</p>
                                <img :src="imagePreview" alt="Current Product Image" 
                                     class="w-full h-48 object-cover rounded-lg border">
                            </div>

                            <!-- Image Upload Area -->
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 text-center">
                                <i class="bi bi-cloud-upload text-2xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-600 mb-2">Upload new image</p>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 10MB</p>
                                
                                <input type="file" name="image" accept="image/*" x-ref="imageInput"
                                       @change="handleImageUpload($event)"
                                       class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            </div>

                            <!-- Image Actions -->
                            <div class="flex space-x-2">
                                <button type="button" @click="$refs.imageInput.click()"
                                        class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md text-sm font-medium transition-colors">
                                    <i class="bi bi-cloud-upload me-1"></i>Change
                                </button>
                                <button type="button" @click="imagePreview = 'https://via.placeholder.com/400x400/f3f4f6/9ca3af?text=Current+Image'; $refs.imageInput.value = ''"
                                        class="px-3 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 text-sm transition-colors">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                            </div>

                            <!-- Image Guidelines -->
                            <div class="text-sm text-gray-600">
                                <p class="font-medium mb-2">Image Guidelines:</p>
                                <ul class="space-y-1 text-xs">
                                    <li>• Recommended size: 800x800px</li>
                                    <li>• Maximum file size: 10MB</li>
                                    <li>• Supported formats: JPG, PNG, GIF</li>
                                    <li>• Use high-quality images for best results</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-between pt-6 border-t">
                <button type="button" class="px-6 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 transition-colors">
                    <i class="bi bi-trash me-2"></i>Delete Product
                </button>
                
                <div class="flex space-x-4">
                    <a href="{{ route('admin.products.show', 1) }}" 
                       class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                        <i class="bi bi-check-circle me-2"></i>Update Product
                    </button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>
