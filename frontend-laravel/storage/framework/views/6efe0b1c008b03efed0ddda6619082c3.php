<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Admin Dashboard - E-Commerce</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-gray-50">
    <!-- Use Unified Navigation -->
    <?php echo $__env->make('layouts.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Title -->
        <h1 class="text-2xl font-bold text-gray-900 mb-8">Admin Dashboard</h1>

        <!-- Welcome Section -->
        <div class="bg-blue-50 rounded-lg p-6 mb-8 flex items-center justify-between">
            <div class="flex items-center">
                <div class="bg-blue-500 text-white rounded-full w-12 h-12 flex items-center justify-center text-lg font-bold mr-4">
                    <?php echo e(strtoupper(substr($user['full_name'] ?? $user['email'] ?? 'A', 0, 2))); ?>

                </div>
                <div>
                    <h2 class="text-xl font-semibold text-gray-900">Welcome back, <?php echo e($user['full_name'] ?? ($user['email'] ?? 'Admin')); ?>!</h2>
                    <p class="text-gray-600">Administrator Dashboard - Full system access</p>
                </div>
            </div>
            <div class="text-right">
                <p class="text-sm text-gray-500">Last login</p>
                <p class="text-sm font-medium text-gray-900"><?php echo e(date('M j, Y g:i A')); ?></p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Orders Management -->
            <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Orders</h3>
                        <p class="text-gray-600 text-sm">Manage customer orders</p>
                    </div>
                    <i class="bi bi-box-seam text-3xl text-blue-600"></i>
                </div>
                <div class="mt-4 space-y-2">
                    <a href="<?php echo e(route('admin.orders.index')); ?>" class="block text-blue-600 hover:text-blue-800 font-medium text-sm">
                        <i class="bi bi-list-ul me-1"></i>All Orders
                    </a>
                    <a href="<?php echo e(route('admin.orders.index', ['status' => 'pending'])); ?>" class="block text-yellow-600 hover:text-yellow-800 font-medium text-sm">
                        <i class="bi bi-clock me-1"></i>Pending Orders
                    </a>
                </div>
            </div>

            <!-- Products Management -->
            <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Products</h3>
                        <p class="text-gray-600 text-sm">Manage product inventory</p>
                    </div>
                    <i class="bi bi-bag text-3xl text-green-600"></i>
                </div>
                <div class="mt-4 space-y-2">
                    <a href="<?php echo e(route('admin.products.create')); ?>" class="block text-green-600 hover:text-green-800 font-medium text-sm">
                        <i class="bi bi-plus-circle me-1"></i>Add Product
                    </a>
                    <a href="<?php echo e(route('admin.products.index')); ?>" class="block text-green-600 hover:text-green-800 font-medium text-sm">
                        <i class="bi bi-list-ul me-1"></i>All Products
                    </a>
                </div>
            </div>

            <!-- Customers Management -->
            <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Customers</h3>
                        <p class="text-gray-600 text-sm">Manage users and customers</p>
                    </div>
                    <i class="bi bi-people text-3xl text-purple-600"></i>
                </div>
                <div class="mt-4 space-y-2">
                    <a href="<?php echo e(route('admin.users.index')); ?>" class="block text-purple-600 hover:text-purple-800 font-medium text-sm">
                        <i class="bi bi-people me-1"></i>All Users
                    </a>
                    <a href="<?php echo e(route('admin.users.roles')); ?>" class="block text-purple-600 hover:text-purple-800 font-medium text-sm">
                        <i class="bi bi-shield-check me-1"></i>User Roles
                    </a>
                </div>
            </div>

            <!-- Payments & Reports -->
            <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Analytics</h3>
                        <p class="text-gray-600 text-sm">Reports and payments</p>
                    </div>
                    <i class="bi bi-graph-up text-3xl text-orange-600"></i>
                </div>
                <div class="mt-4 space-y-2">
                    <a href="<?php echo e(route('admin.payments.index')); ?>" class="block text-orange-600 hover:text-orange-800 font-medium text-sm">
                        <i class="bi bi-credit-card me-1"></i>Payments
                    </a>
                    <a href="<?php echo e(route('admin.reports.sales')); ?>" class="block text-orange-600 hover:text-orange-800 font-medium text-sm">
                        <i class="bi bi-bar-chart me-1"></i>Sales Report
                    </a>
                </div>
            </div>
        </div>

        <!-- Admin Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <!-- Total Orders -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Orders</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo e($stats['total_orders'] ?? 0); ?></p>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="bi bi-box-seam text-blue-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-green-600 text-sm font-medium">+12% from last month</span>
                </div>
            </div>

            <!-- Pending Orders -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Pending Orders</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo e($stats['pending_orders'] ?? 0); ?></p>
                    </div>
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="bi bi-clock text-yellow-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-yellow-600 text-sm font-medium"><?php echo e($stats['pending_orders'] ?? 0); ?> need attention</span>
                </div>
            </div>

            <!-- Total Products -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Products</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo e($stats['total_products'] ?? 0); ?></p>
                    </div>
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="bi bi-bag text-green-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-green-600 text-sm font-medium"><?php echo e($stats['active_products'] ?? 0); ?> active</span>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-900">TZS <?php echo e(number_format($stats['total_revenue'] ?? 0, 2)); ?></p>
                    </div>
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="bi bi-currency-dollar text-purple-600 text-xl"></i>
                    </div>
                </div>
                <div class="mt-4">
                    <span class="text-purple-600 text-sm font-medium">+8% from last month</span>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Recent Orders -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">Recent Orders</h3>
                </div>
                <div class="p-6">
                    <?php if(count($recentOrders ?? []) > 0): ?>
                        <div class="space-y-4">
                            <?php $__currentLoopData = array_slice($recentOrders, 0, 5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">#<?php echo e($order['order_number'] ?? 'N/A'); ?></p>
                                        <p class="text-xs text-gray-500"><?php echo e($order['customer_name'] ?? 'Unknown Customer'); ?></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-900">TZS <?php echo e(number_format($order['total_amount'] ?? 0, 2)); ?></p>
                                        <span class="text-xs px-2 py-1 rounded-full 
                                            <?php if(($order['status'] ?? '') === 'delivered'): ?> bg-green-100 text-green-800
                                            <?php elseif(($order['status'] ?? '') === 'shipped'): ?> bg-blue-100 text-blue-800
                                            <?php elseif(($order['status'] ?? '') === 'pending'): ?> bg-yellow-100 text-yellow-800
                                            <?php elseif(($order['status'] ?? '') === 'cancelled'): ?> bg-red-100 text-red-800
                                            <?php else: ?> bg-gray-100 text-gray-800 <?php endif; ?>">
                                            <?php echo e(ucfirst($order['status'] ?? 'unknown')); ?>

                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-8">
                            <i class="bi bi-box-seam text-gray-400 text-3xl mb-2"></i>
                            <p class="text-gray-500">No recent orders</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- System Status -->
            <div class="bg-white rounded-lg shadow-sm">
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900">System Status</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <!-- JWT Status -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="bg-green-100 p-2 rounded-full mr-3">
                                    <i class="bi bi-shield-check text-green-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">JWT Authentication</p>
                                    <p class="text-xs text-gray-500">Token valid and active</p>
                                </div>
                            </div>
                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">Active</span>
                        </div>

                        <!-- FastAPI Status -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="bg-blue-100 p-2 rounded-full mr-3">
                                    <i class="bi bi-server text-blue-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">FastAPI Backend</p>
                                    <p class="text-xs text-gray-500">API services running</p>
                                </div>
                            </div>
                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">Online</span>
                        </div>

                        <!-- Database Status -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="bg-purple-100 p-2 rounded-full mr-3">
                                    <i class="bi bi-database text-purple-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Database</p>
                                    <p class="text-xs text-gray-500">Supabase connection</p>
                                </div>
                            </div>
                            <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">Connected</span>
                        </div>

                        <!-- Payment Gateway -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="bg-orange-100 p-2 rounded-full mr-3">
                                    <i class="bi bi-credit-card text-orange-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Stripe Gateway</p>
                                    <p class="text-xs text-gray-500">Payment processing</p>
                                </div>
                            </div>
                            <span class="bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full">Active</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<?php /**PATH C:\Users\HACKER JOE\Desktop\MAIN FOLDER\E-commerce FASTAPI AND LARAVEL\frontend-laravel\resources\views/dashboard/admin.blade.php ENDPATH**/ ?>