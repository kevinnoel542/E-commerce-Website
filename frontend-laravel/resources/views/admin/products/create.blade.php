<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Product - Admin</title>
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
                    <span class="text-gray-900">Add New Product</span>
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
            <h1 class="text-2xl font-bold text-gray-900">Add New Product</h1>
            <p class="text-gray-600">Create a new product for your store</p>
        </div>

        <!-- Product Form -->
        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data"
            class="space-y-6" x-data="{
                imagePreview: null,
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
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Main Product Information -->
                <div class="space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Basic Information</h3>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                                <input type="text" name="name" required
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Enter product name">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Description *</label>
                                <textarea name="description" rows="4" required
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Enter product description"></textarea>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                    <select name="category_id"
                                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="">Select Category (Optional)</option>
                                        @if (isset($categories) && !empty($categories))
                                            @foreach ($categories as $category)
                                                <option value="{{ $category['id'] }}">{{ $category['name'] }}</option>
                                            @endforeach
                                        @else
                                            <option value="">No categories available</option>
                                        @endif
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                                    <input type="text" name="brand"
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
                                    <input type="number" name="price" step="0.01" required
                                        class="w-full border border-gray-300 rounded-md pl-8 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="0.00">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Compare Price</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2 text-gray-500">$</span>
                                    <input type="number" name="compare_price" step="0.01"
                                        class="w-full border border-gray-300 rounded-md pl-8 pr-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                        placeholder="0.00">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Stock Quantity *</label>
                                <input type="number" name="stock_quantity" required
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="0">
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
                            <input type="text" name="sku"
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
                                <input type="number" name="weight" step="0.01"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="0.00">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                <select name="status"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                    <option value="draft">Draft</option>
                                </select>
                            </div>
                        </div>

                        <div class="mt-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="featured"
                                    class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
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
                            <!-- Image Upload Area -->
                            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center relative">
                                <div x-show="!imagePreview">
                                    <i class="bi bi-cloud-upload text-4xl text-gray-400 mb-4"></i>
                                    <p class="text-gray-600 mb-2">Click to upload product image</p>
                                    <p class="text-sm text-gray-500">PNG, JPG, GIF up to 10MB</p>
                                    <button type="button" @click="$refs.imageInput.click()"
                                        class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                        Choose Image
                                    </button>
                                </div>

                                <div x-show="imagePreview" class="relative">
                                    <img :src="imagePreview" alt="Preview"
                                        class="max-w-full h-48 object-cover rounded-lg mx-auto">
                                    <button type="button" @click="imagePreview = null; $refs.imageInput.value = ''"
                                        class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                        ×
                                    </button>
                                    <button type="button" @click="$refs.imageInput.click()"
                                        class="mt-3 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                        Change Image
                                    </button>
                                </div>

                                <input type="file" name="image" accept="image/*" x-ref="imageInput"
                                    @change="handleImageUpload($event)" class="hidden">
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
            <div class="flex justify-end space-x-4 pt-6 border-t">
                <a href="{{ route('admin.products.index') }}"
                    class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit"
                    class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                    <i class="bi bi-plus-circle me-2"></i>Create Product
                </button>
            </div>
        </form>
    </div>

    <script>
        // Handle form submission with AJAX
        document.querySelector('form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const submitButton = document.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;

            // Show loading state
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="bi bi-arrow-clockwise animate-spin me-2"></i>Creating...';

            try {
                const formData = new FormData(this);

                // Debug: Log form data
                console.log('Form data being sent:');
                for (let [key, value] of formData.entries()) {
                    console.log(key, value);
                }

                console.log('Submitting to:', this.action);

                const response = await fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                        'Accept': 'application/json'
                    }
                });

                console.log('Response status:', response.status);
                console.log('Response ok:', response.ok);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const contentType = response.headers.get('content-type');
                console.log('Content-Type:', contentType);

                let data;
                if (contentType && contentType.includes('application/json')) {
                    data = await response.json();
                } else {
                    const text = await response.text();
                    console.log('Non-JSON response:', text);
                    throw new Error('Server returned non-JSON response');
                }

                console.log('Response data:', data);

                if (data.success) {
                    showMessage('Product created successfully!', 'success');
                    // Redirect to products list after a short delay
                    setTimeout(() => {
                        window.location.href = '{{ route('admin.products.index') }}';
                    }, 1500);
                } else {
                    showMessage(data.message || 'Failed to create product', 'error');
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                }
            } catch (error) {
                console.error('Detailed error:', error);
                console.error('Error name:', error.name);
                console.error('Error message:', error.message);
                console.error('Error stack:', error.stack);

                let errorMessage = 'Network error occurred';
                if (error.message.includes('Failed to fetch')) {
                    errorMessage = 'Cannot connect to server. Please check if the server is running.';
                } else if (error.message.includes('JSON')) {
                    errorMessage = 'Server returned invalid response. Check server logs.';
                } else {
                    errorMessage = error.message;
                }

                showMessage(errorMessage, 'error');
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        });

        function showMessage(message, type) {
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                info: 'bg-blue-500'
            };

            const messageDiv = document.createElement('div');
            messageDiv.className = `fixed top-4 right-4 ${colors[type]} text-white px-4 py-2 rounded-md shadow-lg z-50`;
            messageDiv.textContent = message;
            document.body.appendChild(messageDiv);

            setTimeout(() => {
                messageDiv.remove();
            }, 3000);
        }
    </script>
</body>

</html>
