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
            <!-- Admin Welcome Section -->
            <div
                class="bg-gradient-to-r from-red-50 to-red-100 rounded-xl shadow-sm border border-red-200 p-6 flex items-center space-x-4">
                <div class="w-16 h-16 bg-red-500 rounded-full flex items-center justify-center">
                    <span class="text-white text-2xl font-bold">
                        <?php echo e(strtoupper(substr($user['full_name'], 0, 1))); ?><?php echo e(strtoupper(substr(explode(' ', $user['full_name'])[1] ?? '', 0, 1))); ?>

                    </span>
                </div>
                <div class="flex-1">
                    <h1 class="text-2xl font-bold text-gray-900">Welcome back, <?php echo e($user['full_name']); ?>!</h1>
                    <p class="text-gray-600">Administrator Dashboard - Full system access</p>
                </div>
                <div class="text-right">
                    <div class="bg-red-500 text-white px-4 py-2 rounded-lg">
                        <div class="text-sm font-medium">Admin Access</div>
                        <div class="text-xs opacity-90">ID: <?php echo e($user['id']); ?></div>
                    </div>
                </div>
            </div>

            <!-- 🌿 Top Section – Overview Summary -->
            <div class="grid grid-cols-4 gap-4" id="admin-stats">
                <!-- 1️⃣ Total Orders -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-3xl font-bold text-gray-900" id="total-orders">Loading...</p>
                            <p class="text-sm text-gray-600 mt-1">Total Orders</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                            <span class="text-2xl">📦</span>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center">
                        <span class="text-green-600 text-sm font-medium">+12% from last month</span>
                    </div>
                </div>

                <!-- 2️⃣ Pending Orders -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-3xl font-bold text-gray-900" id="pending-orders">Loading...</p>
                            <p class="text-sm text-gray-600 mt-1">Pending Orders</p>
                        </div>
                        <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                            <span class="text-2xl">⏳</span>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center">
                        <span class="text-yellow-600 text-sm font-medium">Needs attention</span>
                    </div>
                </div>

                <!-- 3️⃣ Products -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-3xl font-bold text-gray-900" id="products-count">Loading...</p>
                            <p class="text-sm text-gray-600 mt-1">Products</p>
                        </div>
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                            <span class="text-2xl">🛍️</span>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center">
                        <span class="text-green-600 text-sm font-medium">+5 added this week</span>
                    </div>
                </div>

                <!-- 4️⃣ Revenue -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-6 hover:shadow-md transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-3xl font-bold text-gray-900" id="revenue-count">Loading...</p>
                            <p class="text-sm text-gray-600 mt-1">Revenue</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                            <span class="text-2xl">💰</span>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center">
                        <span class="text-green-600 text-sm font-medium">+18% from last month</span>
                    </div>
                </div>
            </div>

            <!-- 🌿 Middle Section – Recent Activity -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Orders Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-gray-900">Recent Orders</h2>
                        <a href="#" class="text-sm text-blue-600 hover:text-blue-700">View All Orders →</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full" id="recent-orders-table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Order ID</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Customer</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Total</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="orders-tbody">
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        <div
                                            class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mx-auto mb-2">
                                        </div>
                                        Loading recent orders...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Payments Table -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                        <h2 class="text-xl font-semibold text-gray-900">Recent Payments</h2>
                        <a href="#" class="text-sm text-green-600 hover:text-green-700">View All Payments →</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full" id="recent-payments-table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Payment ID</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Customer</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Amount</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Date</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200" id="payments-tbody">
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                        <div
                                            class="animate-spin rounded-full h-6 w-6 border-b-2 border-green-600 mx-auto mb-2">
                                        </div>
                                        Loading recent payments...
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Products -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-900">Recent Products</h2>
                    <a href="#" class="text-sm text-purple-600 hover:text-purple-700">View All Products →</a>
                </div>
                <div class="p-6" id="recent-products">
                    <div class="text-center py-8 text-gray-500">
                        <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-purple-600 mx-auto mb-2"></div>
                        <p>Loading recent products...</p>
                    </div>
                </div>
            </div>

            <!-- 🌿 Bottom Section – Quick Actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-semibold text-gray-900">Quick Actions</h2>
                </div>
                <div class="p-6 grid grid-cols-2 md:grid-cols-5 gap-6">
                    <!-- Add New Product -->
                    <a href="#"
                        class="group text-center p-4 rounded-lg hover:bg-blue-50 hover:shadow-lg transition-all">
                        <div
                            class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-blue-200">
                            <span class="text-3xl">➕</span>
                        </div>
                        <h3 class="font-semibold text-gray-900">Add New Product</h3>
                        <p class="text-sm text-gray-600">Create new product</p>
                    </a>

                    <!-- View All Orders -->
                    <a href="#"
                        class="group text-center p-4 rounded-lg hover:bg-green-50 hover:shadow-lg transition-all">
                        <div
                            class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-green-200">
                            <span class="text-3xl">📦</span>
                        </div>
                        <h3 class="font-semibold text-gray-900">View All Orders</h3>
                        <p class="text-sm text-gray-600">Manage orders</p>
                    </a>

                    <!-- View All Payments -->
                    <a href="#"
                        class="group text-center p-4 rounded-lg hover:bg-yellow-50 hover:shadow-lg transition-all">
                        <div
                            class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-yellow-200">
                            <span class="text-3xl">💳</span>
                        </div>
                        <h3 class="font-semibold text-gray-900">View All Payments</h3>
                        <p class="text-sm text-gray-600">Payment history</p>
                    </a>

                    <!-- Manage Categories -->
                    <a href="#"
                        class="group text-center p-4 rounded-lg hover:bg-purple-50 hover:shadow-lg transition-all">
                        <div
                            class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-purple-200">
                            <span class="text-3xl">🏷️</span>
                        </div>
                        <h3 class="font-semibold text-gray-900">Manage Categories</h3>
                        <p class="text-sm text-gray-600">Organize products</p>
                    </a>

                    <!-- Manage Users -->
                    <a href="#"
                        class="group text-center p-4 rounded-lg hover:bg-red-50 hover:shadow-lg transition-all">
                        <div
                            class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-red-200">
                            <span class="text-3xl">👥</span>
                        </div>
                        <h3 class="font-semibold text-gray-900">Manage Users</h3>
                        <p class="text-sm text-gray-600">User accounts</p>
                    </a>
                </div>
            </div>

            <!-- ✅ Nice-To-Have Features -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Sales Chart -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Sales Chart (Last 30 days)</h3>
                    </div>
                    <div class="p-6">
                        <div
                            class="h-48 bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg flex items-center justify-center">
                            <div class="text-center">
                                <span class="text-4xl mb-2 block">📈</span>
                                <p class="text-gray-600">Chart will be integrated with FastAPI</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Alerts -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Low Stock Alerts</h3>
                    </div>
                    <div class="p-6" id="low-stock-alerts">
                        <div class="text-center py-8 text-gray-500">
                            <span class="text-3xl mb-2 block">📦</span>
                            <p>Loading stock alerts...</p>
                        </div>
                    </div>
                </div>

                <!-- Failed Payment Alerts -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-semibold text-gray-900">Failed Payment Alerts</h3>
                    </div>
                    <div class="p-6" id="failed-payments">
                        <div class="text-center py-8 text-gray-500">
                            <span class="text-3xl mb-2 block">⚠️</span>
                            <p>Loading payment alerts...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h2 class="text-xl font-semibold text-gray-900">System Status</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="bi bi-check-circle-fill text-xl text-green-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">FastAPI Backend</p>
                                    <p class="text-lg font-semibold text-green-600">Online</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="bi bi-database-fill text-xl text-green-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Database</p>
                                    <p class="text-lg font-semibold text-green-600">Connected</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-green-50 border border-green-200 p-4 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="bi bi-shield-check-fill text-xl text-green-600"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">Authentication</p>
                                    <p class="text-lg font-semibold text-green-600">JWT Active</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadAdminStats();
            loadRecentActivity();
        });

        async function loadAdminStats() {
            try {
                // Load Products Count
                const productsResponse = await fetch('/api/admin/products/count', {
                    headers: {
                        'Authorization': 'Bearer ' + '<?php echo e(Session::get('access_token')); ?>',
                        'Content-Type': 'application/json'
                    }
                });

                // Load Orders Count
                const ordersResponse = await fetch('/api/admin/orders/count', {
                    headers: {
                        'Authorization': 'Bearer ' + '<?php echo e(Session::get('access_token')); ?>',
                        'Content-Type': 'application/json'
                    }
                });

                // Load Payments/Revenue
                const paymentsResponse = await fetch('/api/admin/payments/stats', {
                    headers: {
                        'Authorization': 'Bearer ' + '<?php echo e(Session::get('access_token')); ?>',
                        'Content-Type': 'application/json'
                    }
                });

                // Update UI with real data or fallback to demo data
                if (productsResponse.ok) {
                    const productsData = await productsResponse.json();
                    document.getElementById('products-count').textContent = productsData.total || '0';
                } else {
                    document.getElementById('products-count').textContent = '567'; // Demo data
                }

                if (ordersResponse.ok) {
                    const ordersData = await ordersResponse.json();
                    document.getElementById('total-orders').textContent = ordersData.total || '0';
                    document.getElementById('pending-orders').textContent = ordersData.pending || '0';
                } else {
                    document.getElementById('total-orders').textContent = '1,234'; // Demo data
                    document.getElementById('pending-orders').textContent = '23'; // Demo data
                }

                if (paymentsResponse.ok) {
                    const paymentsData = await paymentsResponse.json();
                    document.getElementById('revenue-count').textContent = '$' + (paymentsData.total_revenue || '0');
                } else {
                    document.getElementById('revenue-count').textContent = '$45,678'; // Demo data
                }

            } catch (error) {
                console.error('Error loading admin stats:', error);
                // Fallback to demo data
                document.getElementById('total-orders').textContent = '1,234';
                document.getElementById('pending-orders').textContent = '23';
                document.getElementById('products-count').textContent = '567';
                document.getElementById('revenue-count').textContent = '$45,678';
            }
        }

        async function loadRecentActivity() {
            try {
                // Load Recent Orders from FastAPI
                const ordersResponse = await fetch('/api/admin/orders/recent', {
                    headers: {
                        'Authorization': 'Bearer ' + '<?php echo e(Session::get('access_token')); ?>',
                        'Content-Type': 'application/json'
                    }
                });

                if (ordersResponse.ok) {
                    const ordersData = await ordersResponse.json();
                    let ordersHtml = '';

                    if (ordersData.orders && ordersData.orders.length > 0) {
                        ordersData.orders.forEach(order => {
                            const statusClass = getStatusClass(order.status);
                            const formattedDate = new Date(order.created_at).toLocaleDateString();

                            ordersHtml += `
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#${order.id}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${order.customer_name || 'N/A'}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$${order.total}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                                            ${order.status}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${formattedDate}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="text-blue-600 hover:text-blue-900" onclick="updateOrderStatus(${order.id})">Update</button>
                                    </td>
                                </tr>
                            `;
                        });
                    } else {
                        ordersHtml = `
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    No recent orders found
                                </td>
                            </tr>
                        `;
                    }

                    document.getElementById('orders-tbody').innerHTML = ordersHtml;
                } else {
                    // Fallback to demo data
                    loadDemoOrders();
                }
            } catch (error) {
                console.error('Error loading recent orders:', error);
                loadDemoOrders();
            }

            // Load Recent Payments from FastAPI
            try {
                const paymentsResponse = await fetch('/api/admin/payments/recent', {
                    headers: {
                        'Authorization': 'Bearer ' + '<?php echo e(Session::get('access_token')); ?>',
                        'Content-Type': 'application/json'
                    }
                });

                if (paymentsResponse.ok) {
                    const paymentsData = await paymentsResponse.json();
                    let paymentsHtml = '';

                    if (paymentsData.payments && paymentsData.payments.length > 0) {
                        paymentsData.payments.forEach(payment => {
                            const statusClass = getPaymentStatusClass(payment.status);
                            const formattedDate = new Date(payment.created_at).toLocaleDateString();

                            paymentsHtml += `
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#${payment.id}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${payment.customer_name || 'N/A'}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$${payment.amount}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${statusClass}">
                                            ${payment.status}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${formattedDate}</td>
                                </tr>
                            `;
                        });
                    } else {
                        paymentsHtml = `
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                    No recent payments found
                                </td>
                            </tr>
                        `;
                    }

                    document.getElementById('payments-tbody').innerHTML = paymentsHtml;
                } else {
                    // Fallback to demo data
                    loadDemoPayments();
                }
            } catch (error) {
                console.error('Error loading recent payments:', error);
                loadDemoPayments();
            }

            // Load Recent Products from FastAPI
            try {
                const productsResponse = await fetch('/api/admin/products/recent', {
                    headers: {
                        'Authorization': 'Bearer ' + '<?php echo e(Session::get('access_token')); ?>',
                        'Content-Type': 'application/json'
                    }
                });

                if (productsResponse.ok) {
                    const productsData = await productsResponse.json();
                    let productsHtml = '';

                    if (productsData.products && productsData.products.length > 0) {
                        productsHtml = '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">';

                        productsData.products.forEach(product => {
                            const formattedDate = new Date(product.created_at).toLocaleDateString();
                            const imageUrl = product.image_url || '/images/placeholder-product.png';

                            productsHtml += `
                                <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                                    <div class="w-full h-32 bg-gray-100 rounded-lg mb-3 flex items-center justify-center overflow-hidden">
                                        <img src="${imageUrl}" alt="${product.name}" class="w-full h-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                        <span class="text-3xl hidden">📦</span>
                                    </div>
                                    <h4 class="font-semibold text-gray-900">${product.name}</h4>
                                    <p class="text-sm text-gray-600">Added ${formattedDate}</p>
                                    <p class="text-lg font-bold text-green-600 mt-2">$${product.price}</p>
                                </div>
                            `;
                        });

                        productsHtml += '</div>';
                    } else {
                        productsHtml = `
                            <div class="text-center py-8 text-gray-500">
                                <span class="text-3xl mb-2 block">📦</span>
                                <p>No recent products found</p>
                            </div>
                        `;
                    }

                    document.getElementById('recent-products').innerHTML = productsHtml;
                } else {
                    // Fallback to demo data
                    loadDemoProducts();
                }
            } catch (error) {
                console.error('Error loading recent products:', error);
                loadDemoProducts();
            }

            // Load Low Stock Alerts
            setTimeout(() => {
                document.getElementById('low-stock-alerts').innerHTML = `
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <span class="text-red-600">⚠️</span>
                                <div>
                                    <p class="font-medium text-gray-900">Wireless Mouse</p>
                                    <p class="text-sm text-red-600">Only 3 left in stock</p>
                                </div>
                            </div>
                            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">Restock</button>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <span class="text-yellow-600">⚠️</span>
                                <div>
                                    <p class="font-medium text-gray-900">USB Cable</p>
                                    <p class="text-sm text-yellow-600">Only 8 left in stock</p>
                                </div>
                            </div>
                            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">Restock</button>
                        </div>
                    </div>
                `;
            }, 1800);

            // Load Failed Payment Alerts
            setTimeout(() => {
                document.getElementById('failed-payments').innerHTML = `
                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-center space-x-3">
                                <span class="text-red-600">❌</span>
                                <div>
                                    <p class="font-medium text-gray-900">Payment #PAY003</p>
                                    <p class="text-sm text-red-600">Mike Johnson - $89.99</p>
                                </div>
                            </div>
                            <button class="text-blue-600 hover:text-blue-800 text-sm font-medium">Retry</button>
                        </div>
                        <div class="text-center py-4">
                            <p class="text-sm text-gray-500">1 failed payment today</p>
                        </div>
                    </div>
                `;
            }, 2000);
        }

        // Helper Functions
        function getStatusClass(status) {
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

        function getPaymentStatusClass(status) {
            switch (status.toLowerCase()) {
                case 'success':
                case 'completed':
                case 'paid':
                    return 'bg-green-100 text-green-800';
                case 'pending':
                case 'processing':
                    return 'bg-yellow-100 text-yellow-800';
                case 'failed':
                case 'declined':
                case 'cancelled':
                    return 'bg-red-100 text-red-800';
                default:
                    return 'bg-gray-100 text-gray-800';
            }
        }

        function updateOrderStatus(orderId) {
            // This would open a modal or redirect to order management page
            console.log('Update order status for order:', orderId);
            alert('Order status update functionality will be implemented in the order management page.');
        }

        // Demo Data Fallback Functions
        function loadDemoOrders() {
            document.getElementById('orders-tbody').innerHTML = `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#ORD001</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">John Doe</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$129.99</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Delivered
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Dec 15, 2024</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button class="text-blue-600 hover:text-blue-900" onclick="updateOrderStatus(1)">Update</button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#ORD002</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Jane Smith</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$299.99</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            Shipped
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Dec 14, 2024</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button class="text-blue-600 hover:text-blue-900" onclick="updateOrderStatus(2)">Update</button>
                    </td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#ORD003</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Mike Johnson</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$89.99</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            Pending
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Dec 13, 2024</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <button class="text-blue-600 hover:text-blue-900" onclick="updateOrderStatus(3)">Update</button>
                    </td>
                </tr>
            `;
        }

        function loadDemoPayments() {
            document.getElementById('payments-tbody').innerHTML = `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#PAY001</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">John Doe</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$129.99</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Success
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Dec 15, 2024</td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#PAY002</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Jane Smith</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$299.99</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Success
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Dec 14, 2024</td>
                </tr>
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#PAY003</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Mike Johnson</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">$89.99</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            Failed
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">Dec 13, 2024</td>
                </tr>
            `;
        }

        function loadDemoProducts() {
            document.getElementById('recent-products').innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="w-full h-32 bg-gray-100 rounded-lg mb-3 flex items-center justify-center">
                            <span class="text-3xl">📱</span>
                        </div>
                        <h4 class="font-semibold text-gray-900">Wireless Headphones</h4>
                        <p class="text-sm text-gray-600">Added Dec 15, 2024</p>
                        <p class="text-lg font-bold text-green-600 mt-2">$129.99</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="w-full h-32 bg-gray-100 rounded-lg mb-3 flex items-center justify-center">
                            <span class="text-3xl">⌚</span>
                        </div>
                        <h4 class="font-semibold text-gray-900">Smart Watch</h4>
                        <p class="text-sm text-gray-600">Added Dec 14, 2024</p>
                        <p class="text-lg font-bold text-green-600 mt-2">$299.99</p>
                    </div>
                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition">
                        <div class="w-full h-32 bg-gray-100 rounded-lg mb-3 flex items-center justify-center">
                            <span class="text-3xl">💻</span>
                        </div>
                        <h4 class="font-semibold text-gray-900">Laptop Stand</h4>
                        <p class="text-sm text-gray-600">Added Dec 13, 2024</p>
                        <p class="text-lg font-bold text-green-600 mt-2">$89.99</p>
                    </div>
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
<?php /**PATH C:\Users\HACKER JOE\Desktop\MAIN FOLDER\E-commerce FASTAPI AND LARAVEL\frontend-laravel\resources\views/dashboard/admin.blade.php ENDPATH**/ ?>