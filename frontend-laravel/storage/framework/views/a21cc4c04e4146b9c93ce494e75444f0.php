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
            <?php echo e(__('My Orders')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Orders Header -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">My Orders</h1>
                        <p class="text-gray-600">Track and manage your orders</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <select class="border border-gray-300 rounded-lg px-4 py-2 text-sm">
                            <option>All Orders</option>
                            <option>Pending</option>
                            <option>Shipped</option>
                            <option>Delivered</option>
                            <option>Cancelled</option>
                        </select>
                        <input type="text" placeholder="Search orders..." class="border border-gray-300 rounded-lg px-4 py-2 text-sm">
                    </div>
                </div>
            </div>

            <!-- Orders List -->
            <div class="space-y-4" id="orders-list">
                <div class="text-center py-12 text-gray-500">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                    <p class="text-lg">Loading your orders...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadOrders();
        });

        async function loadOrders() {
            try {
                // This would connect to your FastAPI backend
                // const response = await fetch('/api/user/orders');
                // const orders = await response.json();
                
                // For now, show no orders message
                setTimeout(() => {
                    document.getElementById('orders-list').innerHTML = `
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12">
                            <div class="text-center text-gray-500">
                                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                                    <span class="text-4xl">📦</span>
                                </div>
                                <h3 class="text-xl font-semibold text-gray-900 mb-2">No orders found</h3>
                                <p class="text-gray-600 mb-6">You haven't placed any orders yet. Start shopping to see your orders here!</p>
                                <a href="<?php echo e(route('products.index')); ?>" class="bg-blue-600 text-white px-8 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                                    Start Shopping
                                </a>
                            </div>
                        </div>
                    `;
                }, 1000);
            } catch (error) {
                console.error('Error loading orders:', error);
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
<?php /**PATH C:\Users\HACKER JOE\Desktop\MAIN FOLDER\E-commerce FASTAPI AND LARAVEL\frontend-laravel\resources\views/orders/index.blade.php ENDPATH**/ ?>