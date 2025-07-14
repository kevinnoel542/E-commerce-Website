<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Order Details - E-Commerce</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-gray-50">
    <!-- Use Unified Navigation -->
    @include('layouts.navigation')

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Order Details</h1>
                    <p class="text-gray-600 mt-2">Order #{{ $order['order_number'] ?? 'N/A' }}</p>
                </div>
                <a href="{{ route('orders.index') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                    <i class="bi bi-arrow-left me-2"></i>Back to Orders
                </a>
            </div>
        </div>

        <!-- Order Status -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 mb-2">Order Status</h2>
                    <span class="text-xs px-3 py-1 rounded-full 
                        @if(($order['status'] ?? '') === 'delivered') bg-green-100 text-green-800
                        @elseif(($order['status'] ?? '') === 'shipped') bg-blue-100 text-blue-800
                        @elseif(($order['status'] ?? '') === 'pending') bg-yellow-100 text-yellow-800
                        @elseif(($order['status'] ?? '') === 'cancelled') bg-red-100 text-red-800
                        @else bg-gray-100 text-gray-800 @endif">
                        {{ ucfirst($order['status'] ?? 'unknown') }}
                    </span>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">Order Date</p>
                    <p class="font-medium text-gray-900">{{ date('M j, Y g:i A', strtotime($order['created_at'] ?? 'now')) }}</p>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Order Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Order Items</h3>
                    </div>
                    <div class="p-6">
                        @if(count($order['items'] ?? []) > 0)
                            <div class="space-y-4">
                                @foreach($order['items'] as $item)
                                    <div class="flex items-center space-x-4 p-4 border border-gray-200 rounded-lg">
                                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                            @if(isset($item['product']['image_url']) && $item['product']['image_url'])
                                                <img src="{{ $item['product']['image_url'] }}" 
                                                     alt="{{ $item['product']['name'] ?? 'Product' }}"
                                                     class="w-full h-full object-cover rounded-lg">
                                            @else
                                                <i class="bi bi-image text-gray-400 text-xl"></i>
                                            @endif
                                        </div>
                                        <div class="flex-1">
                                            <h4 class="font-medium text-gray-900">{{ $item['product']['name'] ?? 'Unknown Product' }}</h4>
                                            <p class="text-sm text-gray-500">{{ $item['product']['description'] ?? '' }}</p>
                                            <div class="flex items-center justify-between mt-2">
                                                <span class="text-sm text-gray-600">Qty: {{ $item['quantity'] ?? 1 }}</span>
                                                <span class="font-medium text-gray-900">TZS {{ number_format($item['price'] ?? 0, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <i class="bi bi-box-seam text-gray-400 text-3xl mb-2"></i>
                                <p class="text-gray-500">No items found in this order</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="space-y-6">
                <!-- Payment Summary -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Summary</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium">TZS {{ number_format($order['subtotal'] ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Shipping</span>
                            <span class="font-medium">TZS {{ number_format($order['shipping_cost'] ?? 0, 2) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Tax</span>
                            <span class="font-medium">TZS {{ number_format($order['tax_amount'] ?? 0, 2) }}</span>
                        </div>
                        <div class="border-t border-gray-200 pt-3">
                            <div class="flex justify-between">
                                <span class="text-lg font-semibold text-gray-900">Total</span>
                                <span class="text-lg font-bold text-gray-900">TZS {{ number_format($order['total_amount'] ?? 0, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Information -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Shipping Information</h3>
                    <div class="space-y-2">
                        <p class="font-medium text-gray-900">{{ $order['shipping_address']['full_name'] ?? $user['full_name'] ?? 'N/A' }}</p>
                        <p class="text-gray-600">{{ $order['shipping_address']['address_line_1'] ?? 'N/A' }}</p>
                        @if(isset($order['shipping_address']['address_line_2']) && $order['shipping_address']['address_line_2'])
                            <p class="text-gray-600">{{ $order['shipping_address']['address_line_2'] }}</p>
                        @endif
                        <p class="text-gray-600">
                            {{ $order['shipping_address']['city'] ?? 'N/A' }}, 
                            {{ $order['shipping_address']['state'] ?? 'N/A' }} 
                            {{ $order['shipping_address']['postal_code'] ?? '' }}
                        </p>
                        <p class="text-gray-600">{{ $order['shipping_address']['country'] ?? 'Tanzania' }}</p>
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Payment Information</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Payment Method</span>
                            <span class="font-medium">{{ ucfirst($order['payment_method'] ?? 'N/A') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Payment Status</span>
                            <span class="text-xs px-2 py-1 rounded-full 
                                @if(($order['payment_status'] ?? '') === 'paid') bg-green-100 text-green-800
                                @elseif(($order['payment_status'] ?? '') === 'pending') bg-yellow-100 text-yellow-800
                                @elseif(($order['payment_status'] ?? '') === 'failed') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ ucfirst($order['payment_status'] ?? 'unknown') }}
                            </span>
                        </div>
                        @if(isset($order['transaction_id']) && $order['transaction_id'])
                            <div class="flex justify-between">
                                <span class="text-gray-600">Transaction ID</span>
                                <span class="font-mono text-sm">{{ $order['transaction_id'] }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Order Actions -->
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Order Actions</h3>
                    <div class="space-y-3">
                        @if(($order['status'] ?? '') === 'pending' && ($order['payment_status'] ?? '') !== 'paid')
                            <button class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                                <i class="bi bi-credit-card me-2"></i>Complete Payment
                            </button>
                        @endif
                        
                        @if(in_array($order['status'] ?? '', ['pending', 'confirmed']) && ($order['payment_status'] ?? '') !== 'paid')
                            <button class="w-full bg-red-600 text-white px-4 py-2 rounded-md hover:bg-red-700 transition-colors">
                                <i class="bi bi-x-circle me-2"></i>Cancel Order
                            </button>
                        @endif

                        <button class="w-full bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                            <i class="bi bi-download me-2"></i>Download Invoice
                        </button>

                        <a href="{{ route('orders.index') }}" 
                           class="block w-full text-center bg-gray-100 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-200 transition-colors">
                            <i class="bi bi-list me-2"></i>View All Orders
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Timeline -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Order Timeline</h3>
            <div class="space-y-4">
                <div class="flex items-center">
                    <div class="bg-green-500 w-3 h-3 rounded-full mr-4"></div>
                    <div>
                        <p class="font-medium text-gray-900">Order Placed</p>
                        <p class="text-sm text-gray-500">{{ date('M j, Y g:i A', strtotime($order['created_at'] ?? 'now')) }}</p>
                    </div>
                </div>
                
                @if(($order['status'] ?? '') !== 'pending')
                    <div class="flex items-center">
                        <div class="bg-blue-500 w-3 h-3 rounded-full mr-4"></div>
                        <div>
                            <p class="font-medium text-gray-900">Order Confirmed</p>
                            <p class="text-sm text-gray-500">{{ date('M j, Y g:i A', strtotime($order['updated_at'] ?? 'now')) }}</p>
                        </div>
                    </div>
                @endif

                @if(in_array($order['status'] ?? '', ['shipped', 'delivered']))
                    <div class="flex items-center">
                        <div class="bg-yellow-500 w-3 h-3 rounded-full mr-4"></div>
                        <div>
                            <p class="font-medium text-gray-900">Order Shipped</p>
                            <p class="text-sm text-gray-500">Estimated delivery in 2-3 business days</p>
                        </div>
                    </div>
                @endif

                @if(($order['status'] ?? '') === 'delivered')
                    <div class="flex items-center">
                        <div class="bg-green-600 w-3 h-3 rounded-full mr-4"></div>
                        <div>
                            <p class="font-medium text-gray-900">Order Delivered</p>
                            <p class="text-sm text-gray-500">Package delivered successfully</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>

</html>
