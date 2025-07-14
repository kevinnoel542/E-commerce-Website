<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - E-Commerce</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-50">
    <!-- Navigation Bar -->
    <nav class="bg-white shadow-sm border-b" x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <h1 class="text-xl font-bold text-gray-800">E-Commerce</h1>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex space-x-6">
                    <a href="{{ route('dashboard.user') }}"
                        class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium">
                        <i class="bi bi-house me-1"></i>Home
                    </a>
                    <a href="{{ route('products.index') }}"
                        class="text-blue-600 border-b-2 border-blue-600 px-3 py-2 text-sm font-medium">
                        <i class="bi bi-bag me-1"></i>Shop
                    </a>
                    <a href="{{ route('orders.index') }}"
                        class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium">
                        <i class="bi bi-box-seam me-1"></i>My Orders
                    </a>
                    <a href="{{ route('wishlist.index') }}"
                        class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium">
                        <i class="bi bi-heart me-1"></i>Wishlist <span
                            class="bg-red-500 text-white text-xs rounded-full px-2 py-1 ml-1"
                            id="wishlist-count">{{ $wishlistCount ?? 0 }}</span>
                    </a>
                    <a href="{{ route('cart.index') }}"
                        class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium">
                        <i class="bi bi-cart me-1"></i>Cart <span
                            class="bg-blue-500 text-white text-xs rounded-full px-2 py-1 ml-1"
                            id="cart-count">{{ $cartCount ?? 0 }}</span>
                    </a>
                </div>

                <!-- User Menu -->
                <div class="flex items-center">
                    <span class="text-sm text-gray-600 mr-4 hidden sm:inline">User</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline hidden sm:inline">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-red-600 text-sm">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>

                    <!-- Mobile menu button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                        class="md:hidden text-gray-600 hover:text-gray-900">
                        <i class="bi bi-list text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Products</h1>
            <p class="text-gray-600 mt-2">Discover our amazing products</p>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <div class="flex flex-wrap items-center gap-4">
                <!-- Category Filter -->
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Category:</label>
                    <select
                        class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option>All Categories</option>
                        <option>Electronics</option>
                        <option>Clothing</option>
                        <option>Books</option>
                        <option>Home & Garden</option>
                    </select>
                </div>

                <!-- Sort Filter -->
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Sort by:</label>
                    <select
                        class="border border-gray-300 rounded-md px-3 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option>Featured</option>
                        <option>Price: Low to High</option>
                        <option>Price: High to Low</option>
                        <option>Name: A to Z</option>
                        <option>Newest First</option>
                    </select>
                </div>

                <!-- Price Range -->
                <div class="flex items-center space-x-2">
                    <label class="text-sm font-medium text-gray-700">Price:</label>
                    <input type="number" placeholder="Min $"
                        class="w-20 border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <span class="text-gray-500">-</span>
                    <input type="number" placeholder="Max $"
                        class="w-20 border border-gray-300 rounded-md px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <!-- In Stock Filter -->
                <label class="flex items-center space-x-2">
                    <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <span class="text-sm text-gray-700">In Stock Only</span>
                </label>

                <!-- Search -->
                <div class="flex-1 max-w-md ml-auto">
                    <div class="relative">
                        <input type="text" placeholder="Search products..."
                            class="w-full border border-gray-300 rounded-md px-4 py-2 pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button class="absolute right-3 top-2.5 text-gray-400 hover:text-gray-600">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>

                <!-- Advanced Filter Toggle -->
                <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    <i class="bi bi-funnel me-1"></i>Advanced
                </button>
            </div>
        </div>

        <!-- Results Info -->
        <div class="flex justify-between items-center mb-6">
            <p class="text-gray-600">{{ count($products) }} products found</p>

            <!-- View Toggle -->
            <div class="flex items-center space-x-2">
                <span class="text-sm text-gray-700">View:</span>
                <button class="p-2 text-blue-600 bg-blue-50 rounded-md">
                    <i class="bi bi-grid"></i>
                </button>
                <button class="p-2 text-gray-400 hover:text-gray-600 rounded-md">
                    <i class="bi bi-list"></i>
                </button>
            </div>
        </div>

        <!-- Products Grid -->
        @if (empty($products))
            <div class="text-center py-12">
                <div class="mb-6">
                    <i class="bi bi-search text-6xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-medium text-gray-900 mb-2">No products found</h3>
                <p class="text-gray-600">Try adjusting your search or filter criteria.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach ($products as $product)
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                        <div class="aspect-square bg-gray-100 flex items-center justify-center">
                            @if (isset($product['image_url']) && $product['image_url'])
                                <img src="{{ $product['image_url'] }}" alt="{{ $product['name'] }}"
                                    class="w-full h-full object-cover">
                            @else
                                <div class="w-16 h-16 bg-orange-200 rounded-lg flex items-center justify-center">
                                    <i class="bi bi-image text-orange-600 text-2xl"></i>
                                </div>
                            @endif
                        </div>
                        <div class="p-4">
                            <h3 class="font-medium text-gray-900 mb-2">{{ $product['name'] }}</h3>
                            <p class="text-sm text-gray-600 mb-3">{{ Str::limit($product['description'] ?? '', 80) }}
                            </p>
                            <div class="flex items-center justify-between mb-3">
                                <span
                                    class="text-lg font-bold text-gray-900">${{ number_format($product['price'], 2) }}</span>
                                @if (isset($product['stock_quantity']) && $product['stock_quantity'] > 0)
                                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">In
                                        Stock</span>
                                @else
                                    <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">Out of
                                        Stock</span>
                                @endif
                            </div>
                            <div class="flex space-x-2">
                                <button onclick="addToCart('{{ $product['id'] }}')"
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md text-sm font-medium transition-colors"
                                    @if (!isset($product['stock_quantity']) || $product['stock_quantity'] <= 0) disabled @endif>
                                    <i class="bi bi-cart-plus me-1"></i>Add to Cart
                                </button>
                                <button onclick="toggleWishlist('{{ $product['id'] }}')"
                                    class="p-2 border border-gray-300 rounded-md transition-colors wishlist-btn-{{ $product['id'] }} {{ in_array($product['id'], $wishlistItems ?? []) ? 'text-red-600 border-red-300' : 'text-gray-600 hover:text-red-600 hover:border-red-300' }}">
                                    <i
                                        class="bi bi-heart{{ in_array($product['id'], $wishlistItems ?? []) ? '-fill' : '' }}"></i>
                                </button>
                                <a href="{{ route('products.show', $product['id']) }}"
                                    class="p-2 border border-gray-300 rounded-md text-gray-600 hover:text-blue-600 hover:border-blue-300 transition-colors">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <!-- Pagination -->
        <div class="mt-8 flex items-center justify-center">
            <div class="flex space-x-2">
                <button
                    class="px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50 disabled:opacity-50"
                    disabled>
                    Previous
                </button>
                <button class="px-3 py-2 text-sm bg-blue-600 text-white rounded-md">1</button>
                <button class="px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50">2</button>
                <button class="px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50">3</button>
                <button class="px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                    Next
                </button>
            </div>
        </div>
    </div>

    <script>
        // Set up CSRF token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function addToCart(productId) {
            fetch('/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: 1
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update cart count
                        document.getElementById('cart-count').textContent = data.cartCount;

                        // Show success message
                        showMessage('Added to cart successfully!', 'success');
                    } else {
                        showMessage(data.message || 'Failed to add to cart', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('Failed to add to cart', 'error');
                });
        }

        function toggleWishlist(productId) {
            fetch('/wishlist/toggle', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update wishlist count
                        document.getElementById('wishlist-count').textContent = data.wishlistCount;

                        // Update button appearance
                        const button = document.querySelector(`.wishlist-btn-${productId}`);
                        const icon = button.querySelector('i');

                        if (data.action === 'added') {
                            button.className = button.className.replace(
                                'text-gray-600 hover:text-red-600 hover:border-red-300',
                                'text-red-600 border-red-300');
                            icon.className = 'bi bi-heart-fill';
                            showMessage('Added to wishlist!', 'success');
                        } else {
                            button.className = button.className.replace('text-red-600 border-red-300',
                                'text-gray-600 hover:text-red-600 hover:border-red-300');
                            icon.className = 'bi bi-heart';
                            showMessage('Removed from wishlist!', 'info');
                        }
                    } else {
                        showMessage(data.message || 'Failed to update wishlist', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('Failed to update wishlist', 'error');
                });
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
