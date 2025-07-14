<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>Orders Management - Admin</title>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-gray-50">
    <!-- Use Unified Navigation -->
    <?php echo $__env->make('layouts.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Orders Management</h1>
                    <p class="text-gray-600 mt-2">Manage and track all customer orders</p>
                </div>
                <a href="<?php echo e(route('admin.dashboard')); ?>" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                    <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <a href="<?php echo e(route('admin.orders.index')); ?>" 
                       class="py-4 px-1 border-b-2 font-medium text-sm <?php echo e(!request('status') ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                        All Orders
                        <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs"><?php echo e($pagination['total'] ?? 0); ?></span>
                    </a>
                    <a href="<?php echo e(route('admin.orders.index', ['status' => 'pending'])); ?>" 
                       class="py-4 px-1 border-b-2 font-medium text-sm <?php echo e(request('status') === 'pending' ? 'border-yellow-500 text-yellow-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                        Pending
                        <span class="ml-2 bg-yellow-100 text-yellow-900 py-0.5 px-2.5 rounded-full text-xs"><?php echo e(collect($orders ?? [])->where('status', 'pending')->count()); ?></span>
                    </a>
                    <a href="<?php echo e(route('admin.orders.index', ['status' => 'confirmed'])); ?>" 
                       class="py-4 px-1 border-b-2 font-medium text-sm <?php echo e(request('status') === 'confirmed' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                        Confirmed
                        <span class="ml-2 bg-blue-100 text-blue-900 py-0.5 px-2.5 rounded-full text-xs"><?php echo e(collect($orders ?? [])->where('status', 'confirmed')->count()); ?></span>
                    </a>
                    <a href="<?php echo e(route('admin.orders.index', ['status' => 'shipped'])); ?>" 
                       class="py-4 px-1 border-b-2 font-medium text-sm <?php echo e(request('status') === 'shipped' ? 'border-purple-500 text-purple-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                        Shipped
                        <span class="ml-2 bg-purple-100 text-purple-900 py-0.5 px-2.5 rounded-full text-xs"><?php echo e(collect($orders ?? [])->where('status', 'shipped')->count()); ?></span>
                    </a>
                    <a href="<?php echo e(route('admin.orders.index', ['status' => 'delivered'])); ?>" 
                       class="py-4 px-1 border-b-2 font-medium text-sm <?php echo e(request('status') === 'delivered' ? 'border-green-500 text-green-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'); ?>">
                        Delivered
                        <span class="ml-2 bg-green-100 text-green-900 py-0.5 px-2.5 rounded-full text-xs"><?php echo e(collect($orders ?? [])->where('status', 'delivered')->count()); ?></span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Orders Table -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">
                        <?php if(request('status')): ?>
                            <?php echo e(ucfirst(request('status'))); ?> Orders
                        <?php else: ?>
                            All Orders
                        <?php endif; ?>
                    </h3>
                    <div class="flex items-center space-x-4">
                        <!-- Search -->
                        <div class="relative">
                            <input type="text" placeholder="Search orders..." 
                                   class="pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                <i class="bi bi-search text-gray-400"></i>
                            </div>
                        </div>
                        <!-- Export -->
                        <button class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                            <i class="bi bi-download me-2"></i>Export
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <?php if(count($orders ?? []) > 0): ?>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">#<?php echo e($order['order_number'] ?? 'N/A'); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo e($order['id'] ?? 'N/A'); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?php echo e($order['customer_name'] ?? 'Unknown'); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo e($order['customer_email'] ?? 'N/A'); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo e(date('M j, Y', strtotime($order['created_at'] ?? 'now'))); ?>

                                        <div class="text-xs text-gray-500"><?php echo e(date('g:i A', strtotime($order['created_at'] ?? 'now'))); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo e(count($order['items'] ?? [])); ?> items
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        TZS <?php echo e(number_format($order['total_amount'] ?? 0, 2)); ?>

                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            <?php if(($order['status'] ?? '') === 'delivered'): ?> bg-green-100 text-green-800
                                            <?php elseif(($order['status'] ?? '') === 'shipped'): ?> bg-blue-100 text-blue-800
                                            <?php elseif(($order['status'] ?? '') === 'confirmed'): ?> bg-purple-100 text-purple-800
                                            <?php elseif(($order['status'] ?? '') === 'pending'): ?> bg-yellow-100 text-yellow-800
                                            <?php elseif(($order['status'] ?? '') === 'cancelled'): ?> bg-red-100 text-red-800
                                            <?php else: ?> bg-gray-100 text-gray-800 <?php endif; ?>">
                                            <?php echo e(ucfirst($order['status'] ?? 'unknown')); ?>

                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            <?php if(($order['payment_status'] ?? '') === 'paid'): ?> bg-green-100 text-green-800
                                            <?php elseif(($order['payment_status'] ?? '') === 'pending'): ?> bg-yellow-100 text-yellow-800
                                            <?php elseif(($order['payment_status'] ?? '') === 'failed'): ?> bg-red-100 text-red-800
                                            <?php else: ?> bg-gray-100 text-gray-800 <?php endif; ?>">
                                            <?php echo e(ucfirst($order['payment_status'] ?? 'unknown')); ?>

                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <a href="<?php echo e(route('admin.orders.show', $order['id'] ?? 1)); ?>" 
                                               class="text-blue-600 hover:text-blue-900">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <button class="text-green-600 hover:text-green-900" title="Update Status">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button class="text-purple-600 hover:text-purple-900" title="Print Invoice">
                                                <i class="bi bi-printer"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="text-center py-12">
                        <i class="bi bi-box-seam text-gray-400 text-4xl mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No orders found</h3>
                        <p class="text-gray-500">
                            <?php if(request('status')): ?>
                                No <?php echo e(request('status')); ?> orders at the moment.
                            <?php else: ?>
                                No orders have been placed yet.
                            <?php endif; ?>
                        </p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Pagination -->
            <?php if(($pagination['total_pages'] ?? 1) > 1): ?>
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    <div class="flex items-center justify-between">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <a href="#" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Previous
                            </a>
                            <a href="#" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                Next
                            </a>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing <span class="font-medium"><?php echo e((($pagination['current_page'] ?? 1) - 1) * ($pagination['per_page'] ?? 20) + 1); ?></span>
                                    to <span class="font-medium"><?php echo e(min(($pagination['current_page'] ?? 1) * ($pagination['per_page'] ?? 20), $pagination['total'] ?? 0)); ?></span>
                                    of <span class="font-medium"><?php echo e($pagination['total'] ?? 0); ?></span> results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    <!-- Pagination links would go here -->
                                    <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500">
                                        Page <?php echo e($pagination['current_page'] ?? 1); ?> of <?php echo e($pagination['total_pages'] ?? 1); ?>

                                    </span>
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>

</html>
<?php /**PATH C:\Users\HACKER JOE\Desktop\MAIN FOLDER\E-commerce FASTAPI AND LARAVEL\frontend-laravel\resources\views/admin/orders/index.blade.php ENDPATH**/ ?>