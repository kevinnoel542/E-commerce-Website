<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $product['name'] ?? 'Product' }} - E-Commerce</title>
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
                    <a href="{{ route('products.index') }}" class="text-blue-600 border-b-2 border-blue-600 px-3 py-2 text-sm font-medium">
                        <i class="bi bi-bag me-1"></i>Shop
                    </a>
                    <a href="{{ route('orders.index') }}" class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium">
                        <i class="bi bi-box-seam me-1"></i>My Orders
                    </a>
                    <a href="{{ route('wishlist.index') }}" class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium">
                        <i class="bi bi-heart me-1"></i>Wishlist <span class="bg-red-500 text-white text-xs rounded-full px-2 py-1 ml-1" id="wishlist-count">{{ $wishlistCount ?? 0 }}</span>
                    </a>
                    <a href="{{ route('cart.index') }}" class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium">
                        <i class="bi bi-cart me-1"></i>Cart <span class="bg-blue-500 text-white text-xs rounded-full px-2 py-1 ml-1" id="cart-count">{{ $cartCount ?? 0 }}</span>
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
        <!-- Breadcrumb -->
        <nav class="mb-8">
            <ol class="flex items-center space-x-2 text-sm text-gray-600">
                <li><a href="{{ route('home') }}" class="hover:text-blue-600">Home</a></li>
                <li><i class="bi bi-chevron-right"></i></li>
                <li><a href="{{ route('products.index') }}" class="hover:text-blue-600">Products</a></li>
                <li><i class="bi bi-chevron-right"></i></li>
                <li class="text-gray-900">{{ $product['name'] ?? 'Product' }}</li>
            </ol>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <!-- Product Images -->
            <div class="space-y-4">
                <!-- Main Image -->
                <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                    @if(isset($product['image_url']) && $product['image_url'])
                        <img src="{{ $product['image_url'] }}" alt="{{ $product['name'] }}" 
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <div class="w-32 h-32 bg-orange-200 rounded-lg flex items-center justify-center">
                                <i class="bi bi-image text-orange-600 text-4xl"></i>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Product Details -->
            <div class="space-y-6">
                <!-- Product Info -->
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $product['name'] ?? 'Product Name' }}</h1>
                    <p class="text-gray-600 mb-4">{{ $product['description'] ?? 'No description available.' }}</p>
                    
                    <!-- Price -->
                    <div class="flex items-center space-x-4 mb-6">
                        <span class="text-3xl font-bold text-gray-900">${{ number_format($product['price'] ?? 0, 2) }}</span>
                        @if(isset($product['compare_price']) && $product['compare_price'] > $product['price'])
                            <span class="text-xl text-gray-500 line-through">${{ number_format($product['compare_price'], 2) }}</span>
                            <span class="bg-red-100 text-red-800 text-sm font-medium px-2 py-1 rounded">
                                Save ${{ number_format($product['compare_price'] - $product['price'], 2) }}
                            </span>
                        @endif
                    </div>

                    <!-- Stock Status -->
                    <div class="mb-6">
                        @if(isset($product['stock_quantity']) && $product['stock_quantity'] > 0)
                            <div class="flex items-center text-green-600">
                                <i class="bi bi-check-circle me-2"></i>
                                <span class="font-medium">In Stock ({{ $product['stock_quantity'] }} available)</span>
                            </div>
                        @else
                            <div class="flex items-center text-red-600">
                                <i class="bi bi-x-circle me-2"></i>
                                <span class="font-medium">Out of Stock</span>
                            </div>
                        @endif
                    </div>

                    <!-- Product Details -->
                    <div class="border-t border-gray-200 pt-6 mb-6">
                        <dl class="grid grid-cols-1 gap-4">
                            @if(isset($product['category']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Category</dt>
                                    <dd class="text-sm text-gray-900">{{ $product['category'] }}</dd>
                                </div>
                            @endif
                            @if(isset($product['brand']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Brand</dt>
                                    <dd class="text-sm text-gray-900">{{ $product['brand'] }}</dd>
                                </div>
                            @endif
                            @if(isset($product['sku']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">SKU</dt>
                                    <dd class="text-sm text-gray-900">{{ $product['sku'] }}</dd>
                                </div>
                            @endif
                            @if(isset($product['weight']))
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Weight</dt>
                                    <dd class="text-sm text-gray-900">{{ $product['weight'] }} kg</dd>
                                </div>
                            @endif
                        </dl>
                    </div>
                </div>

                <!-- Quantity and Actions -->
                <div class="space-y-4">
                    <!-- Quantity Selector -->
                    <div x-data="{ quantity: 1 }">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                        <div class="flex items-center space-x-3 mb-6">
                            <button @click="quantity = Math.max(1, quantity - 1)" 
                                    class="w-10 h-10 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-50">
                                <i class="bi bi-dash"></i>
                            </button>
                            <span class="w-12 text-center font-medium" x-text="quantity"></span>
                            <button @click="quantity = Math.min(10, quantity + 1)" 
                                    class="w-10 h-10 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-50">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-4">
                            <button @click="addToCart('{{ $product['id'] }}', quantity)" 
                                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 px-6 rounded-lg font-medium transition-colors"
                                    @if(!isset($product['stock_quantity']) || $product['stock_quantity'] <= 0) disabled @endif>
                                <i class="bi bi-cart-plus me-2"></i>Add to Cart
                            </button>
                            <button onclick="toggleWishlist('{{ $product['id'] }}')" 
                                    class="px-6 py-3 border border-gray-300 rounded-lg transition-colors wishlist-btn-{{ $product['id'] }} {{ $inWishlist ? 'text-red-600 border-red-300' : 'text-gray-600 hover:text-red-600 hover:border-red-300' }}">
                                <i class="bi bi-heart{{ $inWishlist ? '-fill' : '' }}"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Features -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Features</h3>
                    <ul class="space-y-2 text-sm text-gray-600">
                        <li class="flex items-center">
                            <i class="bi bi-shield-check text-green-600 me-2"></i>
                            1 Year Warranty
                        </li>
                        <li class="flex items-center">
                            <i class="bi bi-truck text-blue-600 me-2"></i>
                            Free Shipping on Orders Over $50
                        </li>
                        <li class="flex items-center">
                            <i class="bi bi-arrow-return-left text-orange-600 me-2"></i>
                            30-Day Return Policy
                        </li>
                        <li class="flex items-center">
                            <i class="bi bi-headset text-purple-600 me-2"></i>
                            24/7 Customer Support
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Related Products -->
        <div class="mt-16">
            <h2 class="text-2xl font-bold text-gray-900 mb-8">Related Products</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6" id="related-products">
                <!-- Related products will be loaded here via JavaScript -->
            </div>
        </div>
    </div>

    <script>
        // Set up CSRF token for AJAX requests
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        function addToCart(productId, quantity = 1) {
            fetch('/cart/add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity
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
                        button.className = button.className.replace('text-gray-600 hover:text-red-600 hover:border-red-300', 'text-red-600 border-red-300');
                        icon.className = 'bi bi-heart-fill';
                        showMessage('Added to wishlist!', 'success');
                    } else {
                        button.className = button.className.replace('text-red-600 border-red-300', 'text-gray-600 hover:text-red-600 hover:border-red-300');
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

        // Load related products
        document.addEventListener('DOMContentLoaded', function() {
            fetch(`/products/{{ $product['id'] }}/recommendations`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.data.length > 0) {
                        const container = document.getElementById('related-products');
                        container.innerHTML = data.data.map(product => `
                            <div class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                                <div class="aspect-square bg-gray-100 flex items-center justify-center">
                                    ${product.image_url ? 
                                        `<img src="${product.image_url}" alt="${product.name}" class="w-full h-full object-cover">` :
                                        `<div class="w-16 h-16 bg-orange-200 rounded-lg flex items-center justify-center">
                                            <i class="bi bi-image text-orange-600 text-2xl"></i>
                                        </div>`
                                    }
                                </div>
                                <div class="p-4">
                                    <h3 class="font-medium text-gray-900 mb-2">${product.name}</h3>
                                    <p class="text-sm text-gray-600 mb-3">${product.description ? product.description.substring(0, 80) + '...' : ''}</p>
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-lg font-bold text-gray-900">$${product.price.toFixed(2)}</span>
                                        <span class="text-xs ${product.stock_quantity > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'} px-2 py-1 rounded-full">
                                            ${product.stock_quantity > 0 ? 'In Stock' : 'Out of Stock'}
                                        </span>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button onclick="addToCart('${product.id}')" 
                                                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-md text-sm font-medium transition-colors"
                                                ${product.stock_quantity <= 0 ? 'disabled' : ''}>
                                            <i class="bi bi-cart-plus me-1"></i>Add to Cart
                                        </button>
                                        <a href="/products/${product.id}" 
                                           class="p-2 border border-gray-300 rounded-md text-gray-600 hover:text-blue-600 hover:border-blue-300 transition-colors">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        `).join('');
                    }
                })
                .catch(error => {
                    console.error('Error loading related products:', error);
                });
        });
    </script>
</body>
</html>
