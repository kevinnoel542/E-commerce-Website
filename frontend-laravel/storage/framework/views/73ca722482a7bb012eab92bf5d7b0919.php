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
                <?php echo e($product['name'] ?? 'Product Details'); ?>

            </h2>
            <a href="<?php echo e(route('products.index')); ?>"
                class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                <i class="bi bi-arrow-left mr-2"></i>Back to Products
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 p-8">
                    <!-- Product Images -->
                    <div>


                        <!-- Main Image -->
                        <div class="aspect-square bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden mb-4"
                            id="main-image">
                            <?php
                                $hasImages =
                                    isset($product['images']) &&
                                    is_array($product['images']) &&
                                    count($product['images']) > 0;
                                $imageUrl = '';
                                if ($hasImages) {
                                    $imageUrl = $product['images'][0];
                                    // Handle both old and new image URL formats
                                    if (strpos($imageUrl, 'http') !== 0) {
                                        $imageUrl = asset('storage' . $imageUrl);
                                    }
                                }
                            ?>

                            <?php if($hasImages && $imageUrl): ?>
                                <img src="<?php echo e($imageUrl); ?>" alt="<?php echo e($product['name'] ?? 'Product'); ?>"
                                    class="w-full h-full object-cover"
                                    onerror="this.parentElement.innerHTML='<span class=\'text-8xl\'>📦</span>'">
                            <?php else: ?>
                                <span class="text-8xl">📦</span>
                            <?php endif; ?>
                        </div>

                        <!-- Additional Images -->
                        <?php
                            $hasMultipleImages =
                                isset($product['images']) &&
                                is_array($product['images']) &&
                                count($product['images']) > 1;
                        ?>

                        <?php if($hasMultipleImages): ?>
                            <div class="grid grid-cols-4 gap-2">
                                <?php $__currentLoopData = $product['images']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if($index < 4): ?>
                                        <?php
                                            $thumbUrl = $image;
                                            // Handle both old and new image URL formats
                                            if (strpos($thumbUrl, 'http') !== 0) {
                                                $thumbUrl = asset('storage' . $thumbUrl);
                                            }
                                        ?>
                                        <div class="aspect-square bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden cursor-pointer hover:ring-2 hover:ring-blue-500 transition-all"
                                            onclick="changeMainImage('<?php echo e($thumbUrl); ?>', <?php echo e($index); ?>)">
                                            <img src="<?php echo e($thumbUrl); ?>"
                                                alt="<?php echo e($product['name'] ?? 'Product'); ?> - Image <?php echo e($index + 1); ?>"
                                                class="w-full h-full object-cover"
                                                onerror="this.parentElement.innerHTML='<span class=\'text-2xl text-gray-400\'>📷</span>'">
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                <!-- Fill remaining slots if less than 4 images -->
                                <?php for($i = count($product['images']); $i < 4; $i++): ?>
                                    <div class="aspect-square bg-gray-100 rounded-lg flex items-center justify-center">
                                        <span class="text-2xl text-gray-400">📷</span>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        <?php else: ?>
                            <!-- Placeholder thumbnails when no additional images -->
                            <div class="grid grid-cols-4 gap-2">
                                <?php for($i = 0; $i < 4; $i++): ?>
                                    <div class="aspect-square bg-gray-100 rounded-lg flex items-center justify-center">
                                        <span class="text-2xl text-gray-400">📷</span>
                                    </div>
                                <?php endfor; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Product Information -->
                    <div class="space-y-6">
                        <!-- Product Title and Price -->
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 mb-2"><?php echo e($product['name'] ?? 'Product Name'); ?>

                            </h1>
                            <div class="flex items-center space-x-4 mb-4">
                                <span
                                    class="text-3xl font-bold text-green-600">$<?php echo e(number_format($product['price'] ?? 0, 2)); ?></span>
                                <?php if(($product['stock_quantity'] ?? 0) > 0): ?>
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                        <i class="bi bi-check-circle mr-2"></i>In Stock
                                        (<?php echo e($product['stock_quantity']); ?>)
                                    </span>
                                <?php else: ?>
                                    <span
                                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                        <i class="bi bi-x-circle mr-2"></i>Out of Stock
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Product Description -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Description</h3>
                            <p class="text-gray-700 leading-relaxed">
                                <?php echo e($product['description'] ?? 'No description available for this product.'); ?>

                            </p>
                        </div>

                        <!-- Product Details -->
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Product Details</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Category:</span>
                                    <span
                                        class="font-medium"><?php echo e($product['category']['name'] ?? 'Uncategorized'); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Product ID:</span>
                                    <span class="font-medium">#<?php echo e($product['id'] ?? 'N/A'); ?></span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600">Availability:</span>
                                    <span
                                        class="font-medium"><?php echo e(($product['stock_quantity'] ?? 0) > 0 ? 'In Stock' : 'Out of Stock'); ?></span>
                                </div>
                            </div>
                        </div>

                        <!-- Quantity and Actions -->
                        <div class="space-y-4">
                            <div>
                                <label for="quantity"
                                    class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                                <div class="flex items-center space-x-3">
                                    <button onclick="decreaseQuantity()"
                                        class="bg-gray-200 text-gray-600 px-3 py-2 rounded-lg hover:bg-gray-300 transition">
                                        <i class="bi bi-dash"></i>
                                    </button>
                                    <input type="number" id="quantity" value="1" min="1"
                                        max="<?php echo e($product['stock_quantity'] ?? 1); ?>"
                                        class="w-20 text-center border border-gray-300 rounded-lg px-3 py-2">
                                    <button onclick="increaseQuantity()"
                                        class="bg-gray-200 text-gray-600 px-3 py-2 rounded-lg hover:bg-gray-300 transition">
                                        <i class="bi bi-plus"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="space-y-3">
                                <button onclick="addToCart()"
                                    class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg text-lg font-semibold hover:bg-blue-700 transition <?php echo e(($product['stock_quantity'] ?? 0) <= 0 ? 'opacity-50 cursor-not-allowed' : ''); ?>"
                                    <?php echo e(($product['stock_quantity'] ?? 0) <= 0 ? 'disabled' : ''); ?>>
                                    <i class="bi bi-cart-plus mr-2"></i>Add to Cart
                                </button>

                                <div class="grid grid-cols-2 gap-3">
                                    <button onclick="addToWishlist()"
                                        class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition">
                                        <i class="bi bi-heart mr-2"></i>Add to Wishlist
                                    </button>
                                    <button onclick="shareProduct()"
                                        class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition">
                                        <i class="bi bi-share mr-2"></i>Share
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Shipping Info -->
                        <div class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">Shipping Information</h4>
                            <div class="space-y-1 text-sm text-gray-600">
                                <div class="flex items-center">
                                    <i class="bi bi-truck mr-2"></i>
                                    <span>Free shipping on orders over $50</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="bi bi-clock mr-2"></i>
                                    <span>Estimated delivery: 3-5 business days</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="bi bi-arrow-return-left mr-2"></i>
                                    <span>30-day return policy</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const productId = <?php echo e($product['id'] ?? 0); ?>;
        const maxStock = <?php echo e($product['stock_quantity'] ?? 1); ?>;

        function increaseQuantity() {
            const quantityInput = document.getElementById('quantity');
            const currentValue = parseInt(quantityInput.value);
            if (currentValue < maxStock) {
                quantityInput.value = currentValue + 1;
            }
        }

        function decreaseQuantity() {
            const quantityInput = document.getElementById('quantity');
            const currentValue = parseInt(quantityInput.value);
            if (currentValue > 1) {
                quantityInput.value = currentValue - 1;
            }
        }

        async function addToCart() {
            const quantity = parseInt(document.getElementById('quantity').value);
            const productId = '<?php echo e($product['id'] ?? ''); ?>';

            try {
                const response = await fetch('/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: quantity
                    })
                });

                const data = await response.json();
                if (data.success) {
                    showMessage(data.message || 'Product added to cart successfully!', 'success');
                    updateCartCount();
                } else {
                    showMessage(data.message || 'Failed to add product to cart', 'error');
                }
            } catch (error) {
                console.error('Error adding to cart:', error);
                showMessage('Failed to add product to cart', 'error');
            }
        }

        async function updateCartCount() {
            try {
                const response = await fetch('/cart/count');
                const data = await response.json();

                // Update cart count in navigation if it exists
                const cartCountElement = document.querySelector('.cart-count');
                if (cartCountElement) {
                    cartCountElement.textContent = data.count;
                }
            } catch (error) {
                console.error('Error updating cart count:', error);
            }
        }

        async function addToWishlist() {
            const productId = '<?php echo e($product['id'] ?? ''); ?>';

            try {
                const response = await fetch('/wishlist/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        product_id: productId
                    })
                });

                const data = await response.json();
                if (data.success) {
                    showMessage(data.message || 'Product added to wishlist successfully!', 'success');
                    updateWishlistCount();
                } else {
                    showMessage(data.message || 'Failed to add product to wishlist', 'error');
                }
            } catch (error) {
                console.error('Error adding to wishlist:', error);
                showMessage('Failed to add product to wishlist', 'error');
            }
        }

        async function updateWishlistCount() {
            try {
                const response = await fetch('/wishlist/count');
                const data = await response.json();

                // Update wishlist count in navigation if it exists
                const wishlistCountElement = document.querySelector('.wishlist-count');
                if (wishlistCountElement) {
                    wishlistCountElement.textContent = data.count;
                }
            } catch (error) {
                console.error('Error updating wishlist count:', error);
            }
        }

        function changeMainImage(imageUrl, index) {
            const mainImageContainer = document.getElementById('main-image');
            const productName = '<?php echo e($product['name'] ?? 'Product'); ?>';

            mainImageContainer.innerHTML = `
                <img src="${imageUrl}" alt="${productName}" class="w-full h-full object-cover">
            `;

            // Update active thumbnail styling
            const thumbnails = document.querySelectorAll('.grid .cursor-pointer');
            thumbnails.forEach((thumb, i) => {
                if (i === index) {
                    thumb.classList.add('ring-2', 'ring-blue-500');
                } else {
                    thumb.classList.remove('ring-2', 'ring-blue-500');
                }
            });
        }

        function shareProduct() {
            if (navigator.share) {
                navigator.share({
                    title: '<?php echo e($product['name'] ?? 'Product'); ?>',
                    text: 'Check out this product!',
                    url: window.location.href
                });
            } else {
                // Fallback: copy URL to clipboard
                navigator.clipboard.writeText(window.location.href).then(() => {
                    showMessage('Product URL copied to clipboard!', 'success');
                });
            }
        }

        function showMessage(message, type) {
            // Remove existing messages
            const existingMessages = document.querySelectorAll('.alert-message');
            existingMessages.forEach(msg => msg.remove());

            const alertClass = type === 'success' ? 'bg-green-100 border-green-400 text-green-700' :
                'bg-red-100 border-red-400 text-red-700';

            const messageDiv = document.createElement('div');
            messageDiv.className = `alert-message fixed top-4 right-4 z-50 border px-4 py-3 rounded ${alertClass}`;
            messageDiv.innerHTML = `
                <span class="block sm:inline">${message}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentElement.remove()">
                    <svg class="fill-current h-6 w-6" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <title>Close</title>
                        <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                    </svg>
                </span>
            `;

            document.body.appendChild(messageDiv);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (messageDiv.parentElement) {
                    messageDiv.remove();
                }
            }, 5000);
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
<?php /**PATH C:\Users\HACKER JOE\Desktop\MAIN FOLDER\E-commerce FASTAPI AND LARAVEL\frontend-laravel\resources\views/products/show.blade.php ENDPATH**/ ?>