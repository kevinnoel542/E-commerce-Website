<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <?php echo e(__('Shopping Cart')); ?>

            </h2>
            <div class="flex space-x-4">
                <a href="<?php echo e(route('products.index')); ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="bi bi-arrow-left mr-2"></i>Continue Shopping
                </a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            <?php if(session('success')): ?>
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cart Items -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                        <div class="p-6 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-900">Cart Items</h3>
                            <p class="text-sm text-gray-600">Review your items before checkout</p>
                        </div>

                        <div id="cart-items" class="divide-y divide-gray-200">
                            <!-- Cart items will be loaded here -->
                            <div class="p-6 text-center text-gray-500">
                                <i class="bi bi-cart text-4xl mb-4 block"></i>
                                <p>Loading cart items...</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 sticky top-6">
                        <div class="p-6 border-b border-gray-100">
                            <h3 class="text-lg font-semibold text-gray-900">Order Summary</h3>
                        </div>

                        <div id="cart-summary" class="p-6">
                            <!-- Summary will be loaded here -->
                            <div class="text-center text-gray-500">
                                <p>Loading summary...</p>
                            </div>
                        </div>

                        <div class="p-6 border-t border-gray-100">
                            <button id="checkout-btn" onclick="proceedToCheckout()" 
                                    class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg hover:bg-blue-700 transition disabled:bg-gray-400 disabled:cursor-not-allowed"
                                    disabled>
                                <i class="bi bi-credit-card mr-2"></i>Proceed to Checkout
                            </button>
                            
                            <button onclick="clearCart()" 
                                    class="w-full mt-3 bg-red-600 text-white py-2 px-4 rounded-lg hover:bg-red-700 transition">
                                <i class="bi bi-trash mr-2"></i>Clear Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let cartData = [];
        let cartSummary = {};

        // Load cart on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadCartItems();
            loadCartSummary();
        });

        function loadCartItems() {
            // Get cart from session (passed from controller)
            const cart = <?php echo json_encode($cart ?? [], 15, 512) ?>;
            cartData = cart;
            
            if (cart.length === 0) {
                document.getElementById('cart-items').innerHTML = `
                    <div class="p-12 text-center text-gray-500">
                        <i class="bi bi-cart text-6xl mb-4 block"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Your cart is empty</h3>
                        <p class="text-gray-600 mb-4">Add some products to get started</p>
                        <a href="<?php echo e(route('products.index')); ?>" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                            <i class="bi bi-bag mr-2"></i>Shop Now
                        </a>
                    </div>
                `;
                return;
            }

            // Load product details for cart items
            loadProductDetails(cart);
        }

        async function loadProductDetails(cart) {
            try {
                const productIds = cart.map(item => item.product_id);
                const productPromises = productIds.map(id => 
                    fetch(`/api/products/${id}`)
                        .then(response => response.json())
                        .catch(error => {
                            console.error(`Error loading product ${id}:`, error);
                            return null;
                        })
                );

                const products = await Promise.all(productPromises);
                renderCartItems(cart, products);
            } catch (error) {
                console.error('Error loading product details:', error);
                document.getElementById('cart-items').innerHTML = `
                    <div class="p-6 text-center text-red-500">
                        <i class="bi bi-exclamation-triangle text-4xl mb-4 block"></i>
                        <p>Error loading cart items. Please refresh the page.</p>
                    </div>
                `;
            }
        }

        function renderCartItems(cart, products) {
            const cartItemsContainer = document.getElementById('cart-items');
            let html = '';

            cart.forEach((item, index) => {
                const product = products[index];
                if (!product) return;

                const imageUrl = (product.images && product.images.length > 0) 
                    ? (product.images[0].startsWith('http') ? product.images[0] : `<?php echo e(asset('storage')); ?>${product.images[0]}`)
                    : '';

                html += `
                    <div class="p-6 flex items-center space-x-4">
                        <div class="w-20 h-20 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                            ${imageUrl ? 
                                `<img src="${imageUrl}" alt="${product.name}" class="w-full h-full object-cover">` :
                                `<span class="text-2xl">📦</span>`
                            }
                        </div>
                        
                        <div class="flex-1">
                            <h4 class="text-lg font-medium text-gray-900">${product.name}</h4>
                            <p class="text-sm text-gray-600">${product.description ? product.description.substring(0, 100) + '...' : ''}</p>
                            <p class="text-lg font-bold text-green-600 mt-1">$${parseFloat(product.price).toFixed(2)}</p>
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <button onclick="updateQuantity('${item.product_id}', ${item.quantity - 1})" 
                                    class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300 transition">
                                <i class="bi bi-dash"></i>
                            </button>
                            <span class="w-12 text-center font-medium">${item.quantity}</span>
                            <button onclick="updateQuantity('${item.product_id}', ${item.quantity + 1})" 
                                    class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center hover:bg-gray-300 transition">
                                <i class="bi bi-plus"></i>
                            </button>
                        </div>
                        
                        <div class="text-right">
                            <p class="text-lg font-bold text-gray-900">$${(parseFloat(product.price) * item.quantity).toFixed(2)}</p>
                            <button onclick="removeItem('${item.product_id}')" 
                                    class="text-red-600 hover:text-red-800 text-sm mt-1">
                                <i class="bi bi-trash mr-1"></i>Remove
                            </button>
                        </div>
                    </div>
                `;
            });

            cartItemsContainer.innerHTML = html;
        }

        async function loadCartSummary() {
            try {
                const response = await fetch('/cart/summary');
                const data = await response.json();
                
                if (data.error) {
                    throw new Error(data.error);
                }
                
                cartSummary = data.summary;
                renderCartSummary(data.summary);
                
                // Enable checkout button if cart has items
                const checkoutBtn = document.getElementById('checkout-btn');
                if (data.items && data.items.length > 0) {
                    checkoutBtn.disabled = false;
                } else {
                    checkoutBtn.disabled = true;
                }
                
            } catch (error) {
                console.error('Error loading cart summary:', error);
                document.getElementById('cart-summary').innerHTML = `
                    <div class="text-center text-red-500">
                        <p>Error loading summary</p>
                    </div>
                `;
            }
        }

        function renderCartSummary(summary) {
            const summaryContainer = document.getElementById('cart-summary');
            
            summaryContainer.innerHTML = `
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-medium">$${parseFloat(summary.subtotal || 0).toFixed(2)}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Shipping:</span>
                        <span class="font-medium">$${parseFloat(summary.shipping_amount || 0).toFixed(2)}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tax:</span>
                        <span class="font-medium">$${parseFloat(summary.tax_amount || 0).toFixed(2)}</span>
                    </div>
                    ${summary.discount_amount > 0 ? `
                        <div class="flex justify-between text-green-600">
                            <span>Discount:</span>
                            <span>-$${parseFloat(summary.discount_amount).toFixed(2)}</span>
                        </div>
                    ` : ''}
                    <hr class="border-gray-200">
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total:</span>
                        <span class="text-green-600">$${parseFloat(summary.total_amount || 0).toFixed(2)}</span>
                    </div>
                </div>
            `;
        }

        async function updateQuantity(productId, newQuantity) {
            if (newQuantity < 0) return;
            
            try {
                const response = await fetch('/cart/update', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: newQuantity
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    // Reload cart
                    location.reload();
                } else {
                    alert('Error updating cart: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error updating quantity:', error);
                alert('Error updating cart');
            }
        }

        async function removeItem(productId) {
            if (!confirm('Are you sure you want to remove this item from your cart?')) {
                return;
            }
            
            try {
                const response = await fetch('/cart/remove', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        product_id: productId
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error removing item: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Error removing item:', error);
                alert('Error removing item');
            }
        }

        async function clearCart() {
            if (!confirm('Are you sure you want to clear your entire cart?')) {
                return;
            }
            
            try {
                const response = await fetch('/cart/clear', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    location.reload();
                } else {
                    alert('Error clearing cart');
                }
            } catch (error) {
                console.error('Error clearing cart:', error);
                alert('Error clearing cart');
            }
        }

        function proceedToCheckout() {
            window.location.href = '/checkout';
        }
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\Users\HACKER JOE\Desktop\MAIN FOLDER\E-commerce FASTAPI AND LARAVEL\frontend-laravel\resources\views/cart/index.blade.php ENDPATH**/ ?>