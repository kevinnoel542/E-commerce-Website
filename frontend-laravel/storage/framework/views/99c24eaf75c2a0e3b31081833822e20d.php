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
                <?php echo e(__('My Wishlist')); ?>

            </h2>
            <div class="flex space-x-4">
                <a href="<?php echo e(route('products.index')); ?>" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                    <i class="bi bi-arrow-left mr-2"></i>Continue Shopping
                </a>
                <button onclick="clearWishlist()" 
                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition">
                    <i class="bi bi-trash mr-2"></i>Clear All
                </button>
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

            <!-- Wishlist Items -->
            <div class="space-y-4" id="wishlist-items">
                <div class="text-center py-12 text-gray-500">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                    <p class="text-lg">Loading your wishlist...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadWishlist();
        });

        async function loadWishlist() {
            try {
                const response = await fetch('/wishlist/items');
                const data = await response.json();
                
                if (data.error) {
                    throw new Error(data.error);
                }
                
                renderWishlistItems(data.items);
                
            } catch (error) {
                console.error('Error loading wishlist items:', error);
                document.getElementById('wishlist-items').innerHTML = `
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center text-red-500">
                        <i class="bi bi-exclamation-triangle text-4xl mb-4 block"></i>
                        <p>Error loading wishlist items. Please refresh the page.</p>
                    </div>
                `;
            }
        }

        function renderWishlistItems(items) {
            const wishlistItemsContainer = document.getElementById('wishlist-items');
            
            if (items.length === 0) {
                wishlistItemsContainer.innerHTML = `
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12">
                        <div class="text-center text-gray-500">
                            <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                <span class="text-4xl">❤️</span>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">Your wishlist is empty</h3>
                            <p class="text-gray-600 mb-6">Start adding products to your wishlist to save them for later!</p>
                            <a href="<?php echo e(route('products.index')); ?>" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                Browse Products
                            </a>
                        </div>
                    </div>
                `;
                return;
            }

            let html = '';
            items.forEach((product) => {
                const imageUrl = (product.images && product.images.length > 0) 
                    ? (product.images[0].startsWith('http') ? product.images[0] : `<?php echo e(asset('storage')); ?>${product.images[0]}`)
                    : '';

                const inStock = (product.stock_quantity || 0) > 0;

                html += `
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center space-x-4">
                            <div class="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden">
                                ${imageUrl ? 
                                    `<img src="${imageUrl}" alt="${product.name}" class="w-full h-full object-cover">` :
                                    `<span class="text-2xl">📦</span>`
                                }
                            </div>
                            
                            <div class="flex-1">
                                <h4 class="text-lg font-medium text-gray-900">${product.name}</h4>
                                <p class="text-sm text-gray-600 mt-1">${product.description ? product.description.substring(0, 150) + '...' : ''}</p>
                                <div class="flex items-center mt-2 space-x-4">
                                    <p class="text-xl font-bold text-green-600">$${parseFloat(product.price).toFixed(2)}</p>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${inStock ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}">
                                        ${inStock ? 'In Stock' : 'Out of Stock'}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="flex flex-col space-y-2">
                                <button onclick="moveToCart('${product.id}')" 
                                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition ${!inStock ? 'opacity-50 cursor-not-allowed' : ''}"
                                        ${!inStock ? 'disabled' : ''}>
                                    <i class="bi bi-cart-plus mr-1"></i>Add to Cart
                                </button>
                                <button onclick="removeFromWishlist('${product.id}')" 
                                        class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                                    <i class="bi bi-trash mr-1"></i>Remove
                                </button>
                                <a href="/products/${product.id}" 
                                   class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition text-center block">
                                    <i class="bi bi-eye mr-1"></i>View
                                </a>
                            </div>
                        </div>
                    </div>
                `;
            });

            wishlistItemsContainer.innerHTML = html;
        }

        async function moveToCart(productId) {
            try {
                const response = await fetch('/wishlist/move-to-cart', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: 1
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    showMessage(data.message || 'Item moved to cart successfully!', 'success');
                    loadWishlist();
                } else {
                    showMessage(data.message || 'Error moving item to cart', 'error');
                }
            } catch (error) {
                console.error('Error moving to cart:', error);
                showMessage('Error moving item to cart', 'error');
            }
        }

        async function removeFromWishlist(productId) {
            if (!confirm('Are you sure you want to remove this item from your wishlist?')) {
                return;
            }
            
            try {
                const response = await fetch('/wishlist/remove', {
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
                    showMessage(data.message || 'Item removed from wishlist successfully!', 'success');
                    loadWishlist();
                } else {
                    showMessage(data.message || 'Error removing item', 'error');
                }
            } catch (error) {
                console.error('Error removing item:', error);
                showMessage('Error removing item', 'error');
            }
        }

        async function clearWishlist() {
            if (!confirm('Are you sure you want to clear your entire wishlist?')) {
                return;
            }
            
            try {
                const response = await fetch('/wishlist/clear', {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                if (response.ok) {
                    showMessage('Wishlist cleared successfully!', 'success');
                    loadWishlist();
                } else {
                    showMessage('Error clearing wishlist', 'error');
                }
            } catch (error) {
                console.error('Error clearing wishlist:', error);
                showMessage('Error clearing wishlist', 'error');
            }
        }

        function showMessage(message, type) {
            const existingMessages = document.querySelectorAll('.alert-message');
            existingMessages.forEach(msg => msg.remove());

            const messageDiv = document.createElement('div');
            messageDiv.className = `alert-message fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 ${
                type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
            }`;
            messageDiv.textContent = message;

            document.body.appendChild(messageDiv);

            setTimeout(() => {
                messageDiv.remove();
            }, 3000);
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
<?php /**PATH C:\Users\HACKER JOE\Desktop\MAIN FOLDER\E-commerce FASTAPI AND LARAVEL\frontend-laravel\resources\views/wishlist/index.blade.php ENDPATH**/ ?>