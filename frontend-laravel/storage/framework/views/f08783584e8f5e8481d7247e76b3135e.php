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
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?php echo e(__('Dashboard')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            <!-- Welcome Section -->
            <div
                class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl shadow-sm border border-blue-200 p-6 flex items-center space-x-4">
                <div class="w-16 h-16 bg-blue-500 rounded-full flex items-center justify-center">
                    <span class="text-white text-2xl font-bold">
                        <?php echo e(strtoupper(substr($user['full_name'], 0, 1))); ?><?php echo e(strtoupper(substr(explode(' ', $user['full_name'])[1] ?? '', 0, 1))); ?>

                    </span>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Welcome back, <?php echo e($user['full_name']); ?>!</h1>
                    <p class="text-gray-600">Here's your account overview and quick actions</p>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4" id="user-stats">
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 hover:shadow-md transition cursor-pointer"
                    onclick="window.location='<?php echo e(route('orders.index')); ?>'">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <i class="bi bi-cart-fill text-2xl text-blue-600"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900" id="orders-count">Loading...</p>
                            <p class="text-sm text-gray-600">Total Orders</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 hover:shadow-md transition">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <i class="bi bi-credit-card-fill text-2xl text-yellow-600"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900" id="pending-payments">Loading...</p>
                            <p class="text-sm text-gray-600">Pending Payments</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 hover:shadow-md transition cursor-pointer"
                    onclick="window.location='<?php echo e(route('profile.edit')); ?>'">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                            <i class="bi bi-geo-alt-fill text-2xl text-green-600"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900" id="addresses-count">Loading...</p>
                            <p class="text-sm text-gray-600">Saved Addresses</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 hover:shadow-md transition cursor-pointer"
                    onclick="window.location='<?php echo e(route('wishlist.index')); ?>'">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                            <i class="bi bi-heart-fill text-2xl text-red-600"></i>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-gray-900" id="wishlist-count">Loading...</p>
                            <p class="text-sm text-gray-600">Wishlist Items</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-900">Recent Orders</h2>
                    <a href="<?php echo e(route('orders.index')); ?>" class="text-sm text-blue-600 hover:text-blue-700">View All
                        Orders →</a>
                </div>
                <div class="p-6" id="recent-orders">
                    <div class="text-center py-8 text-gray-500">
                        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto mb-4"></div>
                        <p>Loading recent orders...</p>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-semibold text-gray-900">Quick Actions</h2>
                </div>
                <div class="p-6 grid grid-cols-2 md:grid-cols-4 gap-6">
                    <a href="<?php echo e(route('products.index')); ?>"
                        class="group text-center p-4 rounded-lg hover:bg-gray-50 transition">
                        <div
                            class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-gray-200">
                            <i class="bi bi-bag-fill text-3xl text-blue-600"></i>
                        </div>
                        <h3 class="font-semibold text-gray-900">Continue Shopping</h3>
                        <p class="text-sm text-gray-600">Browse our products</p>
                    </a>

                    <a href="<?php echo e(route('profile.edit')); ?>"
                        class="group text-center p-4 rounded-lg hover:bg-gray-50 transition">
                        <div
                            class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-gray-200">
                            <i class="bi bi-person-fill text-3xl text-green-600"></i>
                        </div>
                        <h3 class="font-semibold text-gray-900">Update Profile</h3>
                        <p class="text-sm text-gray-600">Manage your settings</p>
                    </a>

                    <a href="<?php echo e(route('wishlist.index')); ?>"
                        class="group text-center p-4 rounded-lg hover:bg-gray-50 transition">
                        <div
                            class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-gray-200">
                            <i class="bi bi-heart-fill text-3xl text-red-600"></i>
                        </div>
                        <h3 class="font-semibold text-gray-900">View Wishlist</h3>
                        <p class="text-sm text-gray-600">See saved items</p>
                    </a>

                    <a href="<?php echo e(route('orders.index')); ?>"
                        class="group text-center p-4 rounded-lg hover:bg-gray-50 transition">
                        <div
                            class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-gray-200">
                            <i class="bi bi-box-seam-fill text-3xl text-purple-600"></i>
                        </div>
                        <h3 class="font-semibold text-gray-900">Track Orders</h3>
                        <p class="text-sm text-gray-600">Check order status</p>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadUserStats();
            loadRecentOrders();
        });

        async function loadUserStats() {
            try {
                // Load user orders count from FastAPI
                const ordersResponse = await fetch('/api/user/orders/count', {
                    headers: {
                        'Authorization': 'Bearer ' + '<?php echo e(Session::get('access_token')); ?>',
                        'Content-Type': 'application/json'
                    }
                });

                // Load user stats from FastAPI
                const statsResponse = await fetch('/api/user/stats', {
                    headers: {
                        'Authorization': 'Bearer ' + '<?php echo e(Session::get('access_token')); ?>',
                        'Content-Type': 'application/json'
                    }
                });

                // Update orders count
                if (ordersResponse.ok) {
                    const ordersData = await ordersResponse.json();
                    document.getElementById('orders-count').textContent = ordersData.total || '0';
                } else {
                    document.getElementById('orders-count').textContent = '0';
                }

                // Update other stats
                if (statsResponse.ok) {
                    const statsData = await statsResponse.json();
                    document.getElementById('pending-payments').textContent = statsData.pending_payments || '0';
                    document.getElementById('addresses-count').textContent = statsData.addresses_count || '0';
                    document.getElementById('wishlist-count').textContent = statsData.wishlist_count || '0';
                } else {
                    document.getElementById('pending-payments').textContent = '0';
                    document.getElementById('addresses-count').textContent = '0';
                    document.getElementById('wishlist-count').textContent = '0';
                }

            } catch (error) {
                console.error('Error loading user stats:', error);
                // Fallback to zero values
                document.getElementById('orders-count').textContent = '0';
                document.getElementById('pending-payments').textContent = '0';
                document.getElementById('addresses-count').textContent = '0';
                document.getElementById('wishlist-count').textContent = '0';
            }
        }

        async function loadRecentOrders() {
            try {
                // Call FastAPI /orders endpoint with JWT
                const response = await fetch('/api/user/orders', {
                    headers: {
                        'Authorization': 'Bearer ' + '<?php echo e(Session::get('access_token')); ?>',
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();

                    if (data.orders && data.orders.length > 0) {
                        // Display recent orders
                        let ordersHtml = '<div class="space-y-4">';

                        // Show only the first 3 recent orders
                        const recentOrders = data.orders.slice(0, 3);

                        recentOrders.forEach(order => {
                            const statusClass = getOrderStatusClass(order.status);
                            const formattedDate = new Date(order.created_at).toLocaleDateString();

                            ordersHtml += `
                                <div class="bg-gray-50 rounded-lg p-4 hover:bg-gray-100 transition">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-4">
                                            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                                <i class="bi bi-box-seam-fill text-blue-600"></i>
                                            </div>
                                            <div>
                                                <p class="font-semibold text-gray-900">Order #${order.id}</p>
                                                <p class="text-sm text-gray-600">${formattedDate}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-4">
                                            <div class="text-right">
                                                <p class="font-bold text-gray-900">$${order.total}</p>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                                                    ${order.status}
                                                </span>
                                            </div>
                                            <a href="/orders/${order.id}" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });

                        ordersHtml += '</div>';
                        ordersHtml += `
                            <div class="mt-6 text-center">
                                <a href="<?php echo e(route('orders.index')); ?>" class="text-blue-600 hover:text-blue-700 font-medium">
                                    View All Orders →
                                </a>
                            </div>
                        `;

                        document.getElementById('recent-orders').innerHTML = ordersHtml;
                    } else {
                        // No orders found
                        showNoOrdersMessage();
                    }
                } else {
                    // API call failed
                    showNoOrdersMessage();
                }
            } catch (error) {
                console.error('Error loading recent orders:', error);
                showNoOrdersMessage();
            }
        }

        function getOrderStatusClass(status) {
            switch (status.toLowerCase()) {
                case 'delivered':
                case 'completed':
                    return 'bg-green-100 text-green-800';
                case 'shipped':
                case 'processing':
                    return 'bg-blue-100 text-blue-800';
                case 'pending':
                    return 'bg-yellow-100 text-yellow-800';
                case 'cancelled':
                case 'failed':
                    return 'bg-red-100 text-red-800';
                default:
                    return 'bg-gray-100 text-gray-800';
            }
        }

        function showNoOrdersMessage() {
            document.getElementById('recent-orders').innerHTML = `
                <div class="text-center py-8 text-gray-500">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="bi bi-box-seam-fill text-3xl text-gray-500"></i>
                    </div>
                    <p class="text-lg font-medium text-gray-900 mb-2">No orders yet</p>
                    <p class="text-sm text-gray-600 mb-4">Start shopping to see your orders here</p>
                    <a href="<?php echo e(route('products.index')); ?>" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        Browse Products
                    </a>
                </div>
            `;
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
<?php /**PATH C:\Users\HACKER JOE\Desktop\MAIN FOLDER\E-commerce FASTAPI AND LARAVEL\frontend-laravel\resources\views/dashboard/user.blade.php ENDPATH**/ ?>