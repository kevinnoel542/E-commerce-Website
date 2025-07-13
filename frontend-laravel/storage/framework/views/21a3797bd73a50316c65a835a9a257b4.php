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
                <?php echo e(__('Order Management')); ?>

            </h2>
            <div class="space-x-2">
                <button onclick="exportOrders()"
                    class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition">
                    <i class="bi bi-download mr-2"></i>Export Orders
                </button>
                <select id="statusFilter" onchange="filterByStatus()" class="border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">All Orders</option>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="shipped">Shipped</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            <?php if(session('success')): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <?php if(session('error')): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            <!-- Order Statistics -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="bi bi-box-seam text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Total Orders</p>
                            <p class="text-2xl font-bold text-gray-900" id="total-orders">
                                <?php echo e($ordersData['total'] ?? 0); ?></p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-yellow-100 rounded-lg">
                            <i class="bi bi-clock text-yellow-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Pending</p>
                            <p class="text-2xl font-bold text-gray-900" id="pending-orders">0</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 rounded-lg">
                            <i class="bi bi-arrow-repeat text-blue-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Processing</p>
                            <p class="text-2xl font-bold text-gray-900" id="processing-orders">0</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 rounded-lg">
                            <i class="bi bi-truck text-purple-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Shipped</p>
                            <p class="text-2xl font-bold text-gray-900" id="shipped-orders">0</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 rounded-lg">
                            <i class="bi bi-check-circle text-green-600 text-xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">Completed</p>
                            <p class="text-2xl font-bold text-gray-900" id="completed-orders">0</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">All Orders</h3>
                            <p class="text-sm text-gray-600">Manage customer orders and their status</p>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="bulkUpdateStatus()"
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                                <i class="bi bi-arrow-repeat mr-2"></i>Bulk Update
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left">
                                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                </th>
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
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if(isset($ordersData['orders']) && count($ordersData['orders']) > 0): ?>
                                <?php $__currentLoopData = $ordersData['orders']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" class="order-checkbox" value="<?php echo e($order['id']); ?>">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo e($order['order_number'] ?? '#' . substr($order['id'] ?? 'N/A', 0, 8)); ?>

                                            </div>
                                            <div class="text-sm text-gray-500"><?php echo e(count($order['items'] ?? [])); ?> items
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <?php echo e($order['shipping_address']['full_name'] ?? 'N/A'); ?>

                                            </div>
                                            <div class="text-sm text-gray-500">
                                                <?php echo e($order['shipping_address']['phone'] ?? 'N/A'); ?>

                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-bold text-gray-900">
                                                $<?php echo e(number_format($order['final_amount'] ?? 0, 2)); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo e(getStatusClass($order['status'] ?? 'pending')); ?>">
                                                <?php echo e(ucfirst($order['status'] ?? 'pending')); ?>

                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo e(isset($order['created_at']) ? \Carbon\Carbon::parse($order['created_at'])->format('M d, Y') : 'N/A'); ?>

                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                            <a href="<?php echo e(route('admin.orders.show', $order['id'])); ?>"
                                                class="text-blue-600 hover:text-blue-900">
                                                <i class="bi bi-eye"></i> View
                                            </a>
                                            <select onchange="updateOrderStatus(<?php echo e($order['id']); ?>, this.value)"
                                                class="text-sm border border-gray-300 rounded px-2 py-1">
                                                <option value="">Update Status</option>
                                                <option value="pending"
                                                    <?php echo e(($order['status'] ?? '') == 'pending' ? 'selected' : ''); ?>>
                                                    Pending</option>
                                                <option value="processing"
                                                    <?php echo e(($order['status'] ?? '') == 'processing' ? 'selected' : ''); ?>>
                                                    Processing</option>
                                                <option value="shipped"
                                                    <?php echo e(($order['status'] ?? '') == 'shipped' ? 'selected' : ''); ?>>
                                                    Shipped</option>
                                                <option value="completed"
                                                    <?php echo e(($order['status'] ?? '') == 'completed' ? 'selected' : ''); ?>>
                                                    Completed</option>
                                                <option value="cancelled"
                                                    <?php echo e(($order['status'] ?? '') == 'cancelled' ? 'selected' : ''); ?>>
                                                    Cancelled</option>
                                            </select>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <div
                                            class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <span class="text-4xl">📦</span>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 mb-2">No orders found</h3>
                                        <p class="text-gray-600">Orders will appear here when customers place them.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php
        function getStatusClass($status)
        {
            switch (strtolower($status)) {
                case 'completed':
                    return 'bg-green-100 text-green-800';
                case 'shipped':
                    return 'bg-purple-100 text-purple-800';
                case 'processing':
                    return 'bg-blue-100 text-blue-800';
                case 'pending':
                    return 'bg-yellow-100 text-yellow-800';
                case 'cancelled':
                    return 'bg-red-100 text-red-800';
                default:
                    return 'bg-gray-100 text-gray-800';
            }
        }
    ?>

    <script>
        // Load order statistics on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadOrderStats();
        });

        function loadOrderStats() {
            // Count orders by status from the current page data
            const orders = <?php echo json_encode($orders ?? [], 15, 512) ?>;
            const stats = {
                pending: 0,
                processing: 0,
                shipped: 0,
                completed: 0,
                cancelled: 0
            };

            orders.forEach(order => {
                const status = order.status?.toLowerCase() || 'pending';
                if (stats.hasOwnProperty(status)) {
                    stats[status]++;
                }
            });

            document.getElementById('pending-orders').textContent = stats.pending;
            document.getElementById('processing-orders').textContent = stats.processing;
            document.getElementById('shipped-orders').textContent = stats.shipped;
            document.getElementById('completed-orders').textContent = stats.completed;
        }

        function updateOrderStatus(orderId, status) {
            if (!status) return;

            if (confirm('Are you sure you want to update this order status?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/orders/${orderId}/status`;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '<?php echo e(csrf_token()); ?>';

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'PUT';

                const statusField = document.createElement('input');
                statusField.type = 'hidden';
                statusField.name = 'status';
                statusField.value = status;

                form.appendChild(csrfToken);
                form.appendChild(methodField);
                form.appendChild(statusField);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function filterByStatus() {
            const status = document.getElementById('statusFilter').value;
            if (status) {
                window.location.href = `/admin/orders/status/${status}`;
            } else {
                window.location.href = '<?php echo e(route('admin.orders.index')); ?>';
            }
        }

        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.order-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
        }

        function bulkUpdateStatus() {
            const selectedOrders = Array.from(document.querySelectorAll('.order-checkbox:checked')).map(cb => cb.value);
            if (selectedOrders.length === 0) {
                alert('Please select at least one order.');
                return;
            }

            const status = prompt('Enter new status (pending, processing, shipped, completed, cancelled):');
            if (status && ['pending', 'processing', 'shipped', 'completed', 'cancelled'].includes(status.toLowerCase())) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = '<?php echo e(route('admin.orders.bulk-update')); ?>';

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '<?php echo e(csrf_token()); ?>';

                const orderIds = document.createElement('input');
                orderIds.type = 'hidden';
                orderIds.name = 'order_ids';
                orderIds.value = JSON.stringify(selectedOrders);

                const statusField = document.createElement('input');
                statusField.type = 'hidden';
                statusField.name = 'status';
                statusField.value = status.toLowerCase();

                form.appendChild(csrfToken);
                form.appendChild(orderIds);
                form.appendChild(statusField);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function exportOrders() {
            window.location.href = '<?php echo e(route('admin.orders.export')); ?>';
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
<?php /**PATH C:\Users\HACKER JOE\Desktop\MAIN FOLDER\E-commerce FASTAPI AND LARAVEL\frontend-laravel\resources\views/admin/orders/index.blade.php ENDPATH**/ ?>