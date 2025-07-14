<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Dashboard - E-Commerce</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-gray-50">
    <!-- Use Unified Navigation -->
    <?php echo $__env->make('layouts.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Title -->
        <h1 class="text-2xl font-bold text-gray-900 mb-8">Dashboard</h1>

        <!-- Welcome Section -->
        <div class="bg-blue-50 rounded-lg p-6 mb-8 flex items-center">
            <div class="bg-blue-500 text-white rounded-full w-12 h-12 flex items-center justify-center text-lg font-bold mr-4">
                <?php echo e(strtoupper(substr($user['full_name'] ?? ($user['email'] ?? 'U'), 0, 2))); ?>

            </div>
            <div>
                <h2 class="text-xl font-semibold text-gray-900">Welcome back, <?php echo e($user['full_name'] ?? ($user['email'] ?? 'User')); ?>!</h2>
                <p class="text-gray-600">Here's your account overview and quick actions</p>
            </div>
        </div>

        <!-- Quick Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <!-- Orders Card -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Orders</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo e(count($orders ?? [])); ?></p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="bi bi-box-seam text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="<?php echo e(route('orders.index')); ?>" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View All Orders →
                    </a>
                </div>
            </div>

            <!-- Pending Payments Card -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pending Payments</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo e($pendingPayments ?? 0); ?></p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="bi bi-credit-card text-yellow-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-yellow-600 text-sm font-medium"><?php echo e($pendingPayments ?? 0); ?> need attention</span>
                </div>
            </div>

            <!-- Addresses Card -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Saved Addresses</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo e($addressCount ?? 1); ?></p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="bi bi-geo-alt text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="<?php echo e(route('profile.edit')); ?>" class="text-green-600 hover:text-green-800 text-sm font-medium">
                        Manage Addresses →
                    </a>
                </div>
            </div>

            <!-- Wishlist Card -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Wishlist Items</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo e($wishlistCount ?? 0); ?></p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="bi bi-heart text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <a href="<?php echo e(route('wishlist.index')); ?>" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                        View Wishlist →
                    </a>
                </div>
            </div>
        </div>

        <!-- Recent Orders -->
        <div class="bg-white rounded-lg shadow-sm mb-8">
            <div class="p-6 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Orders</h3>
                    <a href="<?php echo e(route('orders.index')); ?>" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View All Orders →
                    </a>
                </div>
            </div>
            <div class="p-6">
                <?php if(count($orders ?? []) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-3 px-4 font-medium text-gray-900">Order #</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-900">Date</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-900">Items</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-900">Total</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-900">Status</th>
                                    <th class="text-left py-3 px-4 font-medium text-gray-900">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = array_slice($orders, 0, 5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="border-b border-gray-100 hover:bg-gray-50">
                                        <td class="py-3 px-4">
                                            <span class="font-medium text-gray-900">#<?php echo e($order['order_number'] ?? 'N/A'); ?></span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="text-gray-600"><?php echo e(date('M j, Y', strtotime($order['created_at'] ?? 'now'))); ?></span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="text-gray-600"><?php echo e(count($order['items'] ?? [])); ?> items</span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="font-medium text-gray-900">TZS <?php echo e(number_format($order['total_amount'] ?? 0, 2)); ?></span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="text-xs px-2 py-1 rounded-full 
                                                <?php if(($order['status'] ?? '') === 'delivered'): ?> bg-green-100 text-green-800
                                                <?php elseif(($order['status'] ?? '') === 'shipped'): ?> bg-blue-100 text-blue-800
                                                <?php elseif(($order['status'] ?? '') === 'pending'): ?> bg-yellow-100 text-yellow-800
                                                <?php elseif(($order['status'] ?? '') === 'cancelled'): ?> bg-red-100 text-red-800
                                                <?php else: ?> bg-gray-100 text-gray-800 <?php endif; ?>">
                                                <?php echo e(ucfirst($order['status'] ?? 'unknown')); ?>

                                            </span>
                                        </td>
                                        <td class="py-3 px-4">
                                            <a href="<?php echo e(route('orders.show', $order['id'] ?? 1)); ?>" 
                                               class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                                View Details
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8">
                        <i class="bi bi-box-seam text-gray-400 text-3xl mb-2"></i>
                        <p class="text-gray-500 mb-4">No orders yet</p>
                        <a href="<?php echo e(route('products.index')); ?>" 
                           class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                            Start Shopping
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Shop Now -->
            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <div class="bg-blue-100 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <i class="bi bi-shop text-blue-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Continue Shopping</h3>
                <p class="text-gray-600 text-sm mb-4">Discover new products and great deals</p>
                <a href="<?php echo e(route('products.index')); ?>" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    Browse Products
                </a>
            </div>

            <!-- Track Orders -->
            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <div class="bg-green-100 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <i class="bi bi-truck text-green-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Track Your Orders</h3>
                <p class="text-gray-600 text-sm mb-4">Check the status of your recent orders</p>
                <a href="<?php echo e(route('orders.index')); ?>" 
                   class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                    View Orders
                </a>
            </div>

            <!-- Manage Profile -->
            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <div class="bg-purple-100 p-4 rounded-full w-16 h-16 mx-auto mb-4 flex items-center justify-center">
                    <i class="bi bi-person-gear text-purple-600 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">Manage Profile</h3>
                <p class="text-gray-600 text-sm mb-4">Update your account information</p>
                <a href="<?php echo e(route('profile.edit')); ?>" 
                   class="bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition-colors">
                    Edit Profile
                </a>
            </div>
        </div>
    </div>
</body>

</html>
<?php /**PATH C:\Users\HACKER JOE\Desktop\MAIN FOLDER\E-commerce FASTAPI AND LARAVEL\frontend-laravel\resources\views/dashboard/user.blade.php ENDPATH**/ ?>