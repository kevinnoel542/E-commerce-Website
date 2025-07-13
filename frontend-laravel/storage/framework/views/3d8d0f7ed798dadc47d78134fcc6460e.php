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
            <?php echo e(__('Order Details')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Order Details -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6" id="order-details">
                <div class="text-center py-12 text-gray-500">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                    <p class="text-lg">Loading order details...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadOrderDetails();
        });

        async function loadOrderDetails() {
            try {
                const orderId = '<?php echo e($id); ?>';
                const response = await fetch(`/api/user/orders/${orderId}`, {
                    headers: {
                        'Authorization': 'Bearer ' + '<?php echo e(Session::get("access_token")); ?>',
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    const order = await response.json();
                    displayOrderDetails(order);
                } else {
                    showOrderNotFound();
                }
            } catch (error) {
                console.error('Error loading order details:', error);
                showOrderNotFound();
            }
        }

        function displayOrderDetails(order) {
            const statusClass = getOrderStatusClass(order.status);
            const formattedDate = new Date(order.created_at).toLocaleDateString();
            
            document.getElementById('order-details').innerHTML = `
                <div class="border-b border-gray-200 pb-6 mb-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Order #${order.id}</h1>
                            <p class="text-gray-600">Placed on ${formattedDate}</p>
                        </div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${statusClass}">
                            ${order.status}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Items</h3>
                        <div class="space-y-4">
                            ${order.items ? order.items.map(item => `
                                <div class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg">
                                    <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <span class="text-2xl">📦</span>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900">${item.product_name || 'Product'}</h4>
                                        <p class="text-sm text-gray-600">Quantity: ${item.quantity || 1}</p>
                                        <p class="text-sm text-gray-600">Price: $${item.price || '0.00'}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-gray-900">$${(item.price * item.quantity).toFixed(2)}</p>
                                    </div>
                                </div>
                            `).join('') : '<p class="text-gray-500">No items found</p>'}
                        </div>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h3>
                        <div class="bg-gray-50 rounded-lg p-4 space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Subtotal:</span>
                                <span class="font-medium">$${order.subtotal || '0.00'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Shipping:</span>
                                <span class="font-medium">$${order.shipping || '0.00'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tax:</span>
                                <span class="font-medium">$${order.tax || '0.00'}</span>
                            </div>
                            <div class="border-t border-gray-200 pt-3">
                                <div class="flex justify-between">
                                    <span class="text-lg font-semibold text-gray-900">Total:</span>
                                    <span class="text-lg font-bold text-gray-900">$${order.total}</span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6">
                            <h4 class="font-semibold text-gray-900 mb-2">Shipping Address</h4>
                            <div class="text-sm text-gray-600">
                                <p>${order.shipping_address?.name || 'N/A'}</p>
                                <p>${order.shipping_address?.address_line_1 || ''}</p>
                                <p>${order.shipping_address?.city || ''}, ${order.shipping_address?.state || ''} ${order.shipping_address?.zip_code || ''}</p>
                                <p>${order.shipping_address?.country || ''}</p>
                            </div>
                        </div>

                        <div class="mt-6">
                            <h4 class="font-semibold text-gray-900 mb-2">Payment Method</h4>
                            <div class="text-sm text-gray-600">
                                <p>${order.payment_method || 'Credit Card'}</p>
                                <p>Status: ${order.payment_status || 'Paid'}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-between">
                    <a href="<?php echo e(route('orders.index')); ?>" class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition">
                        ← Back to Orders
                    </a>
                    <div class="space-x-4">
                        <button class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                            Track Order
                        </button>
                        <button class="border border-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-50 transition">
                            Download Invoice
                        </button>
                    </div>
                </div>
            `;
        }

        function showOrderNotFound() {
            document.getElementById('order-details').innerHTML = `
                <div class="text-center py-12 text-gray-500">
                    <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="text-4xl">❌</span>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Order Not Found</h3>
                    <p class="text-gray-600 mb-6">The order you're looking for doesn't exist or you don't have permission to view it.</p>
                    <a href="<?php echo e(route('orders.index')); ?>" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                        Back to Orders
                    </a>
                </div>
            `;
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
<?php /**PATH C:\Users\HACKER JOE\Desktop\MAIN FOLDER\E-commerce FASTAPI AND LARAVEL\frontend-laravel\resources\views/orders/show.blade.php ENDPATH**/ ?>