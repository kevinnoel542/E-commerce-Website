<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - E-Commerce</title>
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
                    <a href="{{ route('products.index') }}"
                        class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium">
                        <i class="bi bi-bag me-1"></i>Shop
                    </a>
                    <a href="{{ route('cart.index') }}"
                        class="text-blue-600 border-b-2 border-blue-600 px-3 py-2 text-sm font-medium">
                        <i class="bi bi-cart me-1"></i>Cart <span
                            class="bg-blue-500 text-white text-xs rounded-full px-2 py-1 ml-1">{{ $cartCount }}</span>
                    </a>
                    <a href="{{ route('wishlist.index') }}"
                        class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium">
                        <i class="bi bi-heart me-1"></i>Wishlist
                    </a>
                </div>

                <!-- User Menu -->
                <div class="flex items-center">
                    @if (App\Http\Controllers\AuthController::isAuthenticated())
                        @php
                            $user = App\Http\Controllers\AuthController::user();
                        @endphp
                        <span
                            class="text-sm text-gray-600 mr-4 hidden sm:inline">{{ $user['full_name'] ?? ($user['email'] ?? 'User') }}</span>
                        <form method="POST" action="{{ route('logout') }}" class="hidden sm:inline">
                            @csrf
                            <button type="submit" class="text-gray-600 hover:text-red-600 text-sm">
                                <i class="bi bi-box-arrow-right"></i> Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-600 hover:text-blue-600 text-sm mr-4">Login</a>
                        <a href="{{ route('register') }}"
                            class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm hover:bg-blue-700">Register</a>
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Shopping Cart</h1>
            <p class="text-gray-600 mt-2">Review your items and proceed to checkout</p>
        </div>

        @if (empty($cartItems))
            <!-- Empty Cart -->
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <div class="mb-6">
                    <i class="bi bi-cart-x text-6xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-medium text-gray-900 mb-2">Your cart is empty</h3>
                <p class="text-gray-600 mb-6">Looks like you haven't added any items to your cart yet.</p>
                <a href="{{ route('products.index') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    <i class="bi bi-bag me-2"></i>Continue Shopping
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="p-6 border-b">
                            <h2 class="text-lg font-medium text-gray-900">Cart Items ({{ count($cartItems) }})</h2>
                        </div>

                        <div class="divide-y divide-gray-200">
                            @foreach ($cartItems as $item)
                                <div class="p-6" x-data="{ quantity: {{ $item['quantity'] }} }">
                                    <div class="flex items-center space-x-4">
                                        <!-- Product Image -->
                                        <div class="flex-shrink-0">
                                            <div
                                                class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center">
                                                @if (isset($item['product']['image_url']) && $item['product']['image_url'])
                                                    <img src="{{ $item['product']['image_url'] }}"
                                                        alt="{{ $item['product']['name'] }}"
                                                        class="w-full h-full object-cover rounded-lg">
                                                @else
                                                    <i class="bi bi-image text-gray-400 text-2xl"></i>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Product Details -->
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-lg font-medium text-gray-900">
                                                {{ $item['product']['name'] }}</h3>
                                            <p class="text-sm text-gray-600 mt-1">
                                                {{ Str::limit($item['product']['description'] ?? '', 100) }}</p>
                                            <p class="text-lg font-bold text-gray-900 mt-2">
                                                ${{ number_format($item['product']['price'], 2) }}</p>
                                        </div>

                                        <!-- Quantity Controls -->
                                        <div class="flex items-center space-x-3">
                                            <button
                                                @click="updateQuantity('{{ $item['product']['id'] }}', quantity - 1)"
                                                :disabled="quantity <= 1"
                                                class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-50 disabled:opacity-50">
                                                <i class="bi bi-dash"></i>
                                            </button>
                                            <span class="w-8 text-center font-medium" x-text="quantity"></span>
                                            <button
                                                @click="updateQuantity('{{ $item['product']['id'] }}', quantity + 1)"
                                                :disabled="quantity >= 10"
                                                class="w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-50 disabled:opacity-50">
                                                <i class="bi bi-plus"></i>
                                            </button>
                                        </div>

                                        <!-- Subtotal -->
                                        <div class="text-right">
                                            <p class="text-lg font-bold text-gray-900">
                                                ${{ number_format($item['subtotal'], 2) }}</p>
                                        </div>

                                        <!-- Remove Button -->
                                        <button @click="removeFromCart('{{ $item['product']['id'] }}')"
                                            class="text-red-600 hover:text-red-800 p-2">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Cart Actions -->
                        <div class="p-6 bg-gray-50 border-t">
                            <div class="flex justify-between">
                                <button onclick="clearCart()" class="text-red-600 hover:text-red-800 font-medium">
                                    <i class="bi bi-trash me-2"></i>Clear Cart
                                </button>
                                <a href="{{ route('products.index') }}"
                                    class="text-blue-600 hover:text-blue-800 font-medium">
                                    <i class="bi bi-arrow-left me-2"></i>Continue Shopping
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-sm p-6 sticky top-8">
                        <h2 class="text-lg font-medium text-gray-900 mb-4">Order Summary</h2>

                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal</span>
                                <span class="font-medium">${{ number_format($total, 2) }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping</span>
                                <span class="font-medium">Free</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax</span>
                                <span class="font-medium">${{ number_format($total * 0.1, 2) }}</span>
                            </div>
                            <div class="border-t pt-3">
                                <div class="flex justify-between">
                                    <span class="text-lg font-medium text-gray-900">Total</span>
                                    <span
                                        class="text-lg font-bold text-gray-900">${{ number_format($total * 1.1, 2) }}</span>
                                </div>
                            </div>
                        </div>

                        <button onclick="proceedToCheckout()"
                            class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-medium mt-6 transition-colors">
                            <i class="bi bi-credit-card me-2"></i>Proceed to Checkout
                        </button>

                        <div class="mt-4 text-center">
                            <p class="text-sm text-gray-600">
                                <i class="bi bi-shield-check me-1"></i>Secure checkout with SSL encryption
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        // Set up CSRF token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        function updateQuantity(productId, newQuantity) {
            if (newQuantity < 1 || newQuantity > 10) return;

            fetch('/cart/update', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: newQuantity
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload(); // Reload to update totals
                    } else {
                        alert(data.message || 'Failed to update cart');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to update cart');
                });
        }

        function removeFromCart(productId) {
            if (!confirm('Are you sure you want to remove this item?')) return;

            fetch('/cart/remove', {
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

        function clearCart() {
            if (!confirm('Are you sure you want to clear your cart?')) return;

            fetch('/cart/clear', {
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
                        alert(data.message || 'Failed to clear cart');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to clear cart');
                });
        }

        function proceedToCheckout() {
            @if (App\Http\Controllers\AuthController::isAuthenticated())
                // User is authenticated, proceed to checkout
                window.location.href = '{{ route('checkout.index') }}';
            @else
                // User not authenticated, redirect to login
                if (confirm('You need to login to proceed to checkout. Would you like to login now?')) {
                    window.location.href = '{{ route('login') }}';
                }
            @endif
        }
    </script>
</body>

</html>
