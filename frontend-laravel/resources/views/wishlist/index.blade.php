<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wishlist - E-Commerce</title>
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
                        <a href="{{ route('home') }}" class="text-xl font-bold text-gray-800">E-Commerce</a>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex space-x-6">
                    <a href="{{ route('products.index') }}" class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium">
                        <i class="bi bi-bag me-1"></i>Shop
                    </a>
                    <a href="{{ route('cart.index') }}" class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium">
                        <i class="bi bi-cart me-1"></i>Cart
                    </a>
                    <a href="{{ route('wishlist.index') }}" class="text-blue-600 border-b-2 border-blue-600 px-3 py-2 text-sm font-medium">
                        <i class="bi bi-heart me-1"></i>Wishlist <span class="bg-blue-500 text-white text-xs rounded-full px-2 py-1 ml-1">{{ $wishlistCount }}</span>
                    </a>
                </div>

                <!-- User Menu -->
                <div class="flex items-center">
                    @auth
                        <span class="text-sm text-gray-600 mr-4 hidden sm:inline">User</span>
                        <form method="POST" action="{{ route('logout') }}" class="inline hidden sm:inline">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-red-600 text-sm">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 text-sm mr-4">Login</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">Register</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Wishlist</h1>
            <p class="text-gray-600 mt-2">Save your favorite items for later</p>
        </div>

        @if(empty($wishlistItems))
            <!-- Empty Wishlist -->
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <div class="mb-6">
                    <i class="bi bi-heart text-6xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-medium text-gray-900 mb-2">Your wishlist is empty</h3>
                <p class="text-gray-600 mb-6">Start adding items you love to your wishlist.</p>
                <a href="{{ route('products.index') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    <i class="bi bi-bag me-2"></i>Browse Products
                </a>
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <div class="p-6 border-b">
                    <div class="flex justify-between items-center">
                        <h2 class="text-lg font-medium text-gray-900">Wishlist Items ({{ count($wishlistItems) }})</h2>
                        <button @click="clearWishlist()" class="text-red-600 hover:text-red-800 font-medium">
                            <i class="bi bi-trash me-2"></i>Clear All
                        </button>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 p-6">
                    @foreach($wishlistItems as $item)
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                            <!-- Product Image -->
                            <div class="aspect-square bg-gray-100 flex items-center justify-center relative">
                                @if(isset($item['product']['image_url']) && $item['product']['image_url'])
                                    <img src="{{ $item['product']['image_url'] }}" alt="{{ $item['product']['name'] }}" 
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-16 h-16 bg-orange-200 rounded-lg flex items-center justify-center">
                                        <i class="bi bi-image text-orange-600 text-2xl"></i>
                                    </div>
                                @endif
                                
                                <!-- Remove from Wishlist Button -->
                                <button @click="removeFromWishlist('{{ $item['product']['id'] }}')" 
                                        class="absolute top-2 right-2 w-8 h-8 bg-white rounded-full shadow-md flex items-center justify-center text-red-600 hover:text-red-800 hover:bg-red-50">
                                    <i class="bi bi-heart-fill"></i>
                                </button>
                            </div>

                            <!-- Product Details -->
                            <div class="p-4">
                                <h3 class="font-medium text-gray-900 mb-2">{{ $item['product']['name'] }}</h3>
                                <p class="text-sm text-gray-600 mb-3">{{ Str::limit($item['product']['description'] ?? '', 80) }}</p>
                                
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-lg font-bold text-gray-900">${{ number_format($item['product']['price'], 2) }}</span>
                                    @if(isset($item['product']['stock_quantity']) && $item['product']['stock_quantity'] > 0)
                                        <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">In Stock</span>
                                    @else
                                        <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded-full">Out of Stock</span>
                                    @endif
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex space-x-2">
                                    <button @click="addToCart('{{ $item['product']['id'] }}')" 
                                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md text-sm font-medium transition-colors"
                                            @if(isset($item['product']['stock_quantity']) && $item['product']['stock_quantity'] <= 0) disabled @endif>
                                        <i class="bi bi-cart-plus me-1"></i>Add to Cart
                                    </button>
                                    <button @click="moveToCart('{{ $item['product']['id'] }}')" 
                                            class="px-3 py-2 border border-gray-300 rounded-md text-gray-600 hover:text-blue-600 hover:border-blue-300 transition-colors"
                                            title="Move to Cart"
                                            @if(isset($item['product']['stock_quantity']) && $item['product']['stock_quantity'] <= 0) disabled @endif>
                                        <i class="bi bi-arrow-right"></i>
                                    </button>
                                </div>

                                <!-- Added Date -->
                                <p class="text-xs text-gray-500 mt-2">
                                    Added {{ \Carbon\Carbon::parse($item['added_at'])->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Continue Shopping -->
                <div class="p-6 bg-gray-50 border-t text-center">
                    <a href="{{ route('products.index') }}" class="text-blue-600 hover:text-blue-800 font-medium">
                        <i class="bi bi-arrow-left me-2"></i>Continue Shopping
                    </a>
                </div>
            </div>
        @endif
    </div>

    <script>
        // Set up CSRF token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        function removeFromWishlist(productId) {
            fetch('/wishlist/remove', {
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
                    location.reload();
                } else {
                    alert(data.message || 'Failed to remove item');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to remove item');
            });
        }

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
                    // Show success message
                    const message = document.createElement('div');
                    message.className = 'fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded-md shadow-lg z-50';
                    message.textContent = 'Added to cart successfully!';
                    document.body.appendChild(message);
                    
                    setTimeout(() => {
                        message.remove();
                    }, 3000);
                } else {
                    alert(data.message || 'Failed to add to cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to add to cart');
            });
        }

        function moveToCart(productId) {
            fetch('/wishlist/move-to-cart', {
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
                    location.reload();
                } else {
                    alert(data.message || 'Failed to move to cart');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to move to cart');
            });
        }

        function clearWishlist() {
            if (!confirm('Are you sure you want to clear your entire wishlist?')) return;
            
            fetch('/wishlist/clear', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Failed to clear wishlist');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to clear wishlist');
            });
        }
    </script>
</body>
</html>
