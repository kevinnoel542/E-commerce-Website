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
                <?php echo e(__('Product Details')); ?>

            </h2>
            <div class="space-x-2">
                <a href="<?php echo e(route('admin.products.edit', $product['id'])); ?>"
                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                    <i class="bi bi-pencil mr-2"></i>Edit Product
                </a>
                <a href="<?php echo e(route('admin.products.index')); ?>"
                    class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                    <i class="bi bi-arrow-left mr-2"></i>Back to Products
                </a>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Product Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900"><?php echo e($product['name'] ?? 'Product Name'); ?></h3>
                    <p class="text-sm text-gray-600">Product ID: <?php echo e($product['id'] ?? 'N/A'); ?></p>
                </div>

                <div class="p-6">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- LEFT SIDE: Product Images -->
                        <div class="space-y-4">
                            <!-- Main Image -->
                            <div
                                class="aspect-square bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden shadow-sm">
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
                                        class="w-full h-full object-cover rounded-lg"
                                        onerror="this.parentElement.innerHTML='<span class=\'text-6xl text-gray-400\'>📦</span>'">
                                <?php else: ?>
                                    <span class="text-6xl text-gray-400">📦</span>
                                <?php endif; ?>
                            </div>

                            <!-- Additional Images Thumbnails -->
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
                                            <div class="aspect-square bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden cursor-pointer hover:ring-2 hover:ring-blue-500 transition-all shadow-sm"
                                                onclick="changeMainImage('<?php echo e($thumbUrl); ?>', <?php echo e($index); ?>)">
                                                <img src="<?php echo e($thumbUrl); ?>"
                                                    alt="<?php echo e($product['name'] ?? 'Product'); ?> - Image <?php echo e($index + 1); ?>"
                                                    class="w-full h-full object-cover"
                                                    onerror="this.parentElement.innerHTML='<span class=\'text-sm text-gray-400\'>📷</span>'">
                                            </div>
                                        <?php endif; ?>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                                    <!-- Fill remaining slots if less than 4 images -->
                                    <?php for($i = count($product['images']); $i < 4; $i++): ?>
                                        <div
                                            class="aspect-square bg-gray-100 rounded-lg flex items-center justify-center shadow-sm">
                                            <span class="text-sm text-gray-400">📷</span>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            <?php else: ?>
                                <!-- Placeholder thumbnails when single image -->
                                <div class="grid grid-cols-4 gap-2">
                                    <?php for($i = 0; $i < 4; $i++): ?>
                                        <div
                                            class="aspect-square bg-gray-100 rounded-lg flex items-center justify-center shadow-sm">
                                            <span class="text-sm text-gray-400">📷</span>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- RIGHT SIDE: Product Details -->
                        <div class="space-y-6">
                            <!-- Product Name & Status -->
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 mb-3"><?php echo e($product['name'] ?? 'N/A'); ?></h2>

                                <!-- Stock Status -->
                                <div class="mb-4">
                                    <?php if(($product['stock_quantity'] ?? 0) > 0): ?>
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            <i class="bi bi-check-circle mr-2"></i>In Stock
                                            (<?php echo e($product['stock_quantity']); ?> units)
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                            <i class="bi bi-x-circle mr-2"></i>Out of Stock
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Price -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Price</h4>
                                <p class="text-3xl font-bold text-green-600">
                                    $<?php echo e(number_format($product['price'] ?? 0, 2)); ?>

                                </p>
                            </div>

                            <!-- Product Details Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Brand -->
                                <div class="bg-white border border-gray-200 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Brand
                                    </h4>
                                    <p class="text-lg text-gray-900"><?php echo e($product['brand'] ?? 'N/A'); ?></p>
                                </div>

                                <!-- SKU -->
                                <div class="bg-white border border-gray-200 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">SKU</h4>
                                    <p class="text-lg text-gray-900 font-mono"><?php echo e($product['sku'] ?? 'N/A'); ?></p>
                                </div>

                                <!-- Category -->
                                <div class="bg-white border border-gray-200 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Category
                                    </h4>
                                    <p class="text-lg text-gray-900">
                                        <?php echo e($product['category']['name'] ?? 'Uncategorized'); ?></p>
                                </div>

                                <!-- Stock Quantity -->
                                <div class="bg-white border border-gray-200 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Stock
                                    </h4>
                                    <p class="text-lg text-gray-900"><?php echo e($product['stock_quantity'] ?? 0); ?> units</p>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="bg-white border border-gray-200 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Description
                                </h4>
                                <p class="text-gray-700 leading-relaxed">
                                    <?php echo e($product['description'] ?? 'No description available.'); ?>

                                </p>
                            </div>

                            <!-- Timestamps -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Created
                                    </h4>
                                    <p class="text-sm text-gray-900">
                                        <?php echo e(isset($product['created_at']) ? \Carbon\Carbon::parse($product['created_at'])->format('M d, Y \a\t g:i A') : 'N/A'); ?>

                                    </p>
                                </div>

                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Last
                                        Updated</h4>
                                    <p class="text-sm text-gray-900">
                                        <?php echo e(isset($product['updated_at']) ? \Carbon\Carbon::parse($product['updated_at'])->format('M d, Y \a\t g:i A') : 'N/A'); ?>

                                    </p>
                                </div>
                            </div>

                            <!-- Product Status -->
                            <div class="bg-white border border-gray-200 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-3">Status</h4>
                                <div class="flex items-center space-x-4">
                                    <?php if($product['is_active'] ?? true): ?>
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            <i class="bi bi-check-circle mr-2"></i>Active
                                        </span>
                                    <?php else: ?>
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                            <i class="bi bi-x-circle mr-2"></i>Inactive
                                        </span>
                                    <?php endif; ?>

                                    <span class="text-sm text-gray-500">
                                        Product ID: <span class="font-mono"><?php echo e($product['id'] ?? 'N/A'); ?></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="mt-8 pt-6 border-t border-gray-200 flex justify-between">
                    <div class="space-x-4">
                        <a href="<?php echo e(route('admin.products.edit', $product['id'])); ?>"
                            class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                            <i class="bi bi-pencil mr-2"></i>Edit Product
                        </a>
                        <form method="POST" action="<?php echo e(route('admin.products.destroy', $product['id'])); ?>"
                            class="inline"
                            onsubmit="return confirm('Are you sure you want to delete this product? This action cannot be undone.')">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit"
                                class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
                                <i class="bi bi-trash mr-2"></i>Delete Product
                            </button>
                        </form>
                    </div>

                    <a href="<?php echo e(route('admin.products.index')); ?>"
                        class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                        Back to Products
                    </a>
                </div>
            </div>
        </div>
    </div>
    </div>

    <script>
        function changeMainImage(imageUrl, index) {
            const mainImageContainer = document.querySelector('.aspect-square.bg-gray-100');
            const productName = '<?php echo e($product['name'] ?? 'Product'); ?>';

            mainImageContainer.innerHTML = `
                <img src="${imageUrl}" alt="${productName}"
                     class="w-full h-full object-cover rounded-lg"
                     onerror="this.parentElement.innerHTML='<span class=\'text-6xl text-gray-400\'>📦</span>'">
            `;

            // Update active thumbnail styling
            const thumbnails = document.querySelectorAll('.cursor-pointer');
            thumbnails.forEach((thumb, i) => {
                if (i === index) {
                    thumb.classList.add('ring-2', 'ring-blue-500');
                } else {
                    thumb.classList.remove('ring-2', 'ring-blue-500');
                }
            });
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
<?php /**PATH C:\Users\HACKER JOE\Desktop\MAIN FOLDER\E-commerce FASTAPI AND LARAVEL\frontend-laravel\resources\views/admin/products/show.blade.php ENDPATH**/ ?>