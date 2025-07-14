<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products Management - Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-gray-50">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-sm border-b" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-xl font-bold text-gray-800">E-Commerce Admin</h1>
                    </div>
                </div>

                <!-- Admin Navigation Links -->
                <div class="hidden md:flex space-x-6">
                    <a href="{{ route('admin.dashboard') }}"
                        class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium">
                        <i class="bi bi-speedometer2 me-1"></i>Dashboard
                    </a>

                    <!-- Products Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false"
                            class="text-blue-600 border-b-2 border-blue-600 px-3 py-2 text-sm font-medium flex items-center">
                            <i class="bi bi-bag me-1"></i>Products
                            <i class="bi bi-chevron-down ml-1 transition-transform duration-200"
                                :class="{ 'rotate-180': open }"></i>
                        </button>
                        <div x-show="open" x-transition
                            class="absolute left-0 top-full mt-1 w-48 bg-white rounded-md shadow-lg py-1 border z-50">
                            <a href="{{ route('admin.products.index') }}"
                                class="block px-4 py-2 text-sm text-blue-600 bg-blue-50 font-medium">All Products</a>
                            <a href="{{ route('admin.products.create') }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Add Product</a>
                            <a href="#"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Categories</a>
                            <a href="#"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Inventory</a>
                        </div>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="flex items-center">
                    <span class="text-sm text-gray-600 mr-2 hidden sm:inline">Admin</span>
                    <span class="bg-red-500 text-white text-xs px-2 py-1 rounded mr-4 hidden sm:inline">Admin</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline hidden sm:inline">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-red-600 text-sm">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="flex justify-between items-center mb-8">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Products Management</h1>
                <p class="text-gray-600">Manage your product catalog</p>
            </div>
            <a href="{{ route('admin.products.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="bi bi-plus-circle me-2"></i>Add New Product
            </a>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                    <select
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option>All Categories</option>
                        <option>Electronics</option>
                        <option>Clothing</option>
                        <option>Books</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option>All Status</option>
                        <option>Active</option>
                        <option>Inactive</option>
                        <option>Out of Stock</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sort By</label>
                    <select
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option>Name A-Z</option>
                        <option>Name Z-A</option>
                        <option>Price Low-High</option>
                        <option>Price High-Low</option>
                        <option>Newest First</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <div class="relative">
                        <input type="text" placeholder="Search products..."
                            class="w-full border border-gray-300 rounded-md px-3 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <i class="bi bi-search absolute right-3 top-3 text-gray-400"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products Table -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Product</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Price</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @if (empty($products))
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <i class="bi bi-box-seam text-4xl mb-4"></i>
                                        <p class="text-lg font-medium">No products found</p>
                                        <p class="text-sm">Start by creating your first product.</p>
                                    </div>
                                </td>
                            </tr>
                        @else
                            @foreach ($products as $product)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-16 w-16 flex-shrink-0">
                                                <img class="h-16 w-16 rounded-lg object-cover"
                                                    src="{{ $product['image_url'] ?? 'https://via.placeholder.com/64x64/f3f4f6/9ca3af?text=IMG' }}"
                                                    alt="{{ $product['name'] ?? 'Product' }}"
                                                    onerror="this.src='https://via.placeholder.com/64x64/f3f4f6/9ca3af?text=IMG'">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    {{ $product['name'] ?? 'Unknown Product' }}</div>
                                                <div class="text-sm text-gray-500">
                                                    {{ Str::limit($product['description'] ?? '', 60) }}</div>
                                                @if (isset($product['sku']))
                                                    <div class="text-xs text-gray-400">SKU: {{ $product['sku'] }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if (isset($product['category']))
                                            <span
                                                class="px-2 py-1 text-xs font-medium bg-blue-100 text-blue-800 rounded-full">{{ $product['category'] }}</span>
                                        @else
                                            <span
                                                class="px-2 py-1 text-xs font-medium bg-gray-100 text-gray-800 rounded-full">Uncategorized</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ${{ number_format($product['price'] ?? 0, 2) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $product['stock_quantity'] ?? 0 }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if (isset($product['is_active']) && $product['is_active'])
                                            <span
                                                class="px-2 py-1 text-xs font-medium bg-green-100 text-green-800 rounded-full">Active</span>
                                        @else
                                            <span
                                                class="px-2 py-1 text-xs font-medium bg-red-100 text-red-800 rounded-full">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                        <a href="{{ route('admin.products.show', $product['id']) }}"
                                            class="text-blue-600 hover:text-blue-900" title="View Product">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product['id']) }}"
                                            class="text-green-600 hover:text-green-900" title="Edit Product">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button
                                            onclick="deleteProduct('{{ $product['id'] }}', '{{ $product['name'] }}')"
                                            class="text-red-600 hover:text-red-900" title="Delete Product">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if (isset($pagination) && isset($pagination['total_pages']) && $pagination['total_pages'] > 1)
            <div class="mt-6 flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    Showing <span
                        class="font-medium">{{ ($pagination['page'] - 1) * $pagination['per_page'] + 1 }}</span>
                    to <span
                        class="font-medium">{{ min($pagination['page'] * $pagination['per_page'], $pagination['total']) }}</span>
                    of <span class="font-medium">{{ $pagination['total'] }}</span> results
                </div>
                <div class="flex space-x-2">
                    @if ($pagination['page'] > 1)
                        <a href="?page={{ $pagination['page'] - 1 }}"
                            class="px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50">Previous</a>
                    @endif

                    @for ($i = max(1, $pagination['page'] - 2); $i <= min($pagination['total_pages'], $pagination['page'] + 2); $i++)
                        @if ($i == $pagination['page'])
                            <span
                                class="px-3 py-2 text-sm bg-blue-600 text-white rounded-md">{{ $i }}</span>
                        @else
                            <a href="?page={{ $i }}"
                                class="px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50">{{ $i }}</a>
                        @endif
                    @endfor

                    @if ($pagination['page'] < $pagination['total_pages'])
                        <a href="?page={{ $pagination['page'] + 1 }}"
                            class="px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50">Next</a>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <script>
        // Set up CSRF token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function deleteProduct(productId, productName) {
            if (confirm(`Are you sure you want to delete "${productName}"? This action cannot be undone.`)) {
                fetch(`/dashboard/admin/products/${productId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showMessage('Product deleted successfully!', 'success');
                            // Reload the page after a short delay
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            showMessage(data.message || 'Failed to delete product', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        showMessage('Failed to delete product', 'error');
                    });
            }
        }

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
