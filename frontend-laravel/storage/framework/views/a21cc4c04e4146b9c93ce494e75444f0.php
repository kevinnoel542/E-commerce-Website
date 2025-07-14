<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - E-Commerce</title>
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
                        <a href="<?php echo e(route('home')); ?>" class="text-xl font-bold text-gray-800">E-Commerce</a>
                    </div>
                </div>

                <!-- Navigation Links -->
                <div class="hidden md:flex space-x-6">
                    <a href="<?php echo e(route('products.index')); ?>" class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium">
                        <i class="bi bi-bag me-1"></i>Shop
                    </a>
                    <a href="<?php echo e(route('orders.index')); ?>" class="text-blue-600 border-b-2 border-blue-600 px-3 py-2 text-sm font-medium">
                        <i class="bi bi-box-seam me-1"></i>My Orders
                    </a>
                    <a href="<?php echo e(route('cart.index')); ?>" class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium">
                        <i class="bi bi-cart me-1"></i>Cart
                    </a>
                    <a href="<?php echo e(route('wishlist.index')); ?>" class="text-gray-600 hover:text-blue-600 px-3 py-2 text-sm font-medium">
                        <i class="bi bi-heart me-1"></i>Wishlist
                    </a>
                </div>

                <!-- User Menu -->
                <div class="flex items-center">
                    <span class="text-sm text-gray-600 mr-4 hidden sm:inline"><?php echo e($user['full_name'] ?? $user['email'] ?? 'User'); ?></span>
                    <form method="POST" action="<?php echo e(route('logout')); ?>" class="inline hidden sm:inline">
                        <?php echo csrf_field(); ?>
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
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">My Orders</h1>
            <p class="text-gray-600 mt-2">Track and manage your orders</p>
        </div>

        <?php if(empty($orders)): ?>
            <!-- No Orders -->
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <div class="mb-6">
                    <i class="bi bi-box-seam text-6xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-medium text-gray-900 mb-2">No orders yet</h3>
                <p class="text-gray-600 mb-6">You haven't placed any orders yet. Start shopping to see your orders here.</p>
                <a href="<?php echo e(route('products.index')); ?>" 
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    <i class="bi bi-bag me-2"></i>Start Shopping
                </a>
            </div>
        <?php else: ?>
            <!-- Orders List -->
            <div class="space-y-6">
                <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <!-- Order Header -->
                        <div class="p-6 border-b bg-gray-50">
                            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                                <div class="flex items-center space-x-4">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">Order #<?php echo e($order['id'] ?? 'N/A'); ?></h3>
                                        <p class="text-sm text-gray-600">
                                            Placed on <?php echo e(isset($order['created_at']) ? \Carbon\Carbon::parse($order['created_at'])->format('M d, Y') : 'N/A'); ?>

                                        </p>
                                    </div>
                                </div>
                                
                                <div class="mt-4 md:mt-0 flex items-center space-x-4">
                                    <!-- Order Status -->
                                    <?php
                                        $status = $order['status'] ?? 'pending';
                                        $statusColors = [
                                            'pending' => 'bg-yellow-100 text-yellow-800',
                                            'confirmed' => 'bg-blue-100 text-blue-800',
                                            'processing' => 'bg-purple-100 text-purple-800',
                                            'shipped' => 'bg-indigo-100 text-indigo-800',
                                            'delivered' => 'bg-green-100 text-green-800',
                                            'cancelled' => 'bg-red-100 text-red-800'
                                        ];
                                        $statusColor = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';
                                    ?>
                                    <span class="px-3 py-1 text-sm font-medium rounded-full <?php echo e($statusColor); ?>">
                                        <?php echo e(ucfirst($status)); ?>

                                    </span>
                                    
                                    <!-- Order Total -->
                                    <span class="text-lg font-bold text-gray-900">
                                        $<?php echo e(number_format($order['total_amount'] ?? 0, 2)); ?>

                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Order Items -->
                        <div class="p-6">
                            <?php if(isset($order['items']) && is_array($order['items'])): ?>
                                <div class="space-y-4">
                                    <?php $__currentLoopData = $order['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="flex items-center space-x-4">
                                            <!-- Product Image -->
                                            <div class="flex-shrink-0">
                                                <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                                                    <?php if(isset($item['product']['image_url']) && $item['product']['image_url']): ?>
                                                        <img src="<?php echo e($item['product']['image_url']); ?>" alt="<?php echo e($item['product']['name'] ?? 'Product'); ?>" 
                                                             class="w-full h-full object-cover rounded-lg">
                                                    <?php else: ?>
                                                        <i class="bi bi-image text-gray-400"></i>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <!-- Product Details -->
                                            <div class="flex-1 min-w-0">
                                                <h4 class="text-sm font-medium text-gray-900"><?php echo e($item['product']['name'] ?? 'Product Name'); ?></h4>
                                                <p class="text-sm text-gray-600">Quantity: <?php echo e($item['quantity'] ?? 1); ?></p>
                                                <p class="text-sm font-medium text-gray-900">$<?php echo e(number_format($item['price'] ?? 0, 2)); ?> each</p>
                                            </div>

                                            <!-- Item Total -->
                                            <div class="text-right">
                                                <p class="text-sm font-medium text-gray-900">
                                                    $<?php echo e(number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2)); ?>

                                                </p>
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            <?php else: ?>
                                <p class="text-gray-600">No items found for this order.</p>
                            <?php endif; ?>
                        </div>

                        <!-- Order Actions -->
                        <div class="px-6 py-4 bg-gray-50 border-t">
                            <div class="flex justify-between items-center">
                                <div class="text-sm text-gray-600">
                                    <?php if(isset($order['shipping_address'])): ?>
                                        <i class="bi bi-geo-alt me-1"></i>
                                        Shipping to: <?php echo e($order['shipping_address']['city'] ?? ''); ?>, <?php echo e($order['shipping_address']['state'] ?? ''); ?>

                                    <?php endif; ?>
                                </div>
                                
                                <div class="flex space-x-3">
                                    <a href="<?php echo e(route('orders.show', $order['id'])); ?>" 
                                       class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        <i class="bi bi-eye me-1"></i>View Details
                                    </a>
                                    
                                    <?php if($status === 'delivered'): ?>
                                        <button class="text-green-600 hover:text-green-800 text-sm font-medium">
                                            <i class="bi bi-arrow-repeat me-1"></i>Reorder
                                        </button>
                                    <?php endif; ?>
                                    
                                    <?php if(in_array($status, ['pending', 'confirmed'])): ?>
                                        <button class="text-red-600 hover:text-red-800 text-sm font-medium">
                                            <i class="bi bi-x-circle me-1"></i>Cancel
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <!-- Pagination -->
            <?php if(isset($pagination) && $pagination['total_pages'] > 1): ?>
                <div class="mt-8 flex items-center justify-center">
                    <div class="flex space-x-2">
                        <?php if($pagination['current_page'] > 1): ?>
                            <a href="?page=<?php echo e($pagination['current_page'] - 1); ?>" 
                               class="px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                                Previous
                            </a>
                        <?php endif; ?>
                        
                        <?php for($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <?php if($i == $pagination['current_page']): ?>
                                <span class="px-3 py-2 text-sm bg-blue-600 text-white rounded-md"><?php echo e($i); ?></span>
                            <?php else: ?>
                                <a href="?page=<?php echo e($i); ?>" 
                                   class="px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50"><?php echo e($i); ?></a>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if($pagination['current_page'] < $pagination['total_pages']): ?>
                            <a href="?page=<?php echo e($pagination['current_page'] + 1); ?>" 
                               class="px-3 py-2 text-sm border border-gray-300 rounded-md hover:bg-gray-50">
                                Next
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>
</html>
<?php /**PATH C:\Users\HACKER JOE\Desktop\MAIN FOLDER\E-commerce FASTAPI AND LARAVEL\frontend-laravel\resources\views/orders/index.blade.php ENDPATH**/ ?>