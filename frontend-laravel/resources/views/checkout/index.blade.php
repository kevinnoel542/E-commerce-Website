<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Checkout - E-Commerce</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-gray-50">
    @include('layouts.navigation')

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900">Checkout</h1>
            <p class="text-gray-600 mt-2">Complete your order</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Checkout Form -->
            <div class="lg:col-span-2">
                <form id="checkoutForm" class="space-y-8">
                    @csrf

                    <!-- Shipping Address -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">
                            <i class="bi bi-geo-alt me-2"></i>Shipping Address
                        </h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">Full Name
                                    *</label>
                                <input type="text" id="full_name" name="shipping_address[full_name]" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    value="{{ $user['full_name'] ?? '' }}">
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone Number
                                    *</label>
                                <input type="tel" id="phone" name="shipping_address[phone]" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    value="{{ $user['phone'] ?? '' }}" placeholder="+255 XXX XXX XXX">
                            </div>

                            <div class="md:col-span-2">
                                <label for="street" class="block text-sm font-medium text-gray-700 mb-2">Street
                                    Address *</label>
                                <input type="text" id="street" name="shipping_address[street]" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    placeholder="Street address, P.O. Box, company name">
                            </div>

                            <div>
                                <label for="city" class="block text-sm font-medium text-gray-700 mb-2">City
                                    *</label>
                                <input type="text" id="city" name="shipping_address[city]" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="state" class="block text-sm font-medium text-gray-700 mb-2">State/Region
                                    *</label>
                                <input type="text" id="state" name="shipping_address[state]" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">Postal
                                    Code *</label>
                                <input type="text" id="postal_code" name="shipping_address[postal_code]" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label for="country" class="block text-sm font-medium text-gray-700 mb-2">Country
                                    *</label>
                                <select id="country" name="shipping_address[country]" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="Tanzania" selected>Tanzania</option>
                                    <option value="Kenya">Kenya</option>
                                    <option value="Uganda">Uganda</option>
                                    <option value="Rwanda">Rwanda</option>
                                    <option value="Burundi">Burundi</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">
                            <i class="bi bi-credit-card me-2"></i>Payment Method
                        </h2>

                        <div class="space-y-4">
                            <div class="flex items-center">
                                <input id="stripe" name="payment_method" type="radio" value="stripe" checked
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <label for="stripe" class="ml-3 block text-sm font-medium text-gray-700">
                                    <i class="bi bi-credit-card me-2"></i>Credit/Debit Card (Stripe)
                                </label>
                            </div>

                            <div class="flex items-center">
                                <input id="bank_transfer" name="payment_method" type="radio" value="bank_transfer"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300">
                                <label for="bank_transfer" class="ml-3 block text-sm font-medium text-gray-700">
                                    <i class="bi bi-bank me-2"></i>Bank Transfer
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Order Notes -->
                    <div class="bg-white rounded-lg shadow-sm p-6">
                        <h2 class="text-xl font-semibold text-gray-900 mb-6">
                            <i class="bi bi-chat-text me-2"></i>Order Notes (Optional)
                        </h2>

                        <textarea id="notes" name="notes" rows="4"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Special instructions for your order..."></textarea>
                    </div>
                </form>
            </div>

            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6 sticky top-4">
                    <h2 class="text-xl font-semibold text-gray-900 mb-6">Order Summary</h2>

                    <div id="orderSummary">
                        <div class="text-center py-8">
                            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
                            <p class="text-gray-500 mt-2">Loading order summary...</p>
                        </div>
                    </div>

                    <button type="button" onclick="placeOrder()"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 px-4 rounded-lg font-medium mt-6 transition-colors">
                        <i class="bi bi-check-circle me-2"></i>Place Order
                    </button>

                    <div class="mt-4 text-center">
                        <p class="text-sm text-gray-600">
                            <i class="bi bi-shield-check me-1"></i>Secure checkout with SSL encryption
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Load order summary on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadOrderSummary();
        });

        async function loadOrderSummary() {
            try {
                const response = await fetch('/cart/summary', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });

                const data = await response.json();

                if (data.success) {
                    displayOrderSummary(data);
                } else {
                    document.getElementById('orderSummary').innerHTML =
                        '<p class="text-red-500 text-center">Failed to load order summary</p>';
                }
            } catch (error) {
                console.error('Error loading order summary:', error);
                document.getElementById('orderSummary').innerHTML =
                    '<p class="text-red-500 text-center">Error loading order summary</p>';
            }
        }

        function displayOrderSummary(data) {
            const summary = data.summary;
            const items = data.items || [];

            let html = '';

            // Cart items
            if (items.length > 0) {
                html += '<div class="space-y-3 mb-4">';
                items.forEach(item => {
                    html += `
                        <div class="flex justify-between items-center">
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-900">${item.name}</p>
                                <p class="text-xs text-gray-500">Qty: ${item.quantity}</p>
                            </div>
                            <p class="text-sm font-medium text-gray-900">TZS ${parseFloat(item.total).toLocaleString()}</p>
                        </div>
                    `;
                });
                html += '</div>';
            }

            // Summary totals
            html += `
                <div class="border-t pt-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="text-gray-900">TZS ${parseFloat(summary.subtotal || 0).toLocaleString()}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Shipping:</span>
                        <span class="text-gray-900">TZS ${parseFloat(summary.shipping || 0).toLocaleString()}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Tax:</span>
                        <span class="text-gray-900">TZS ${parseFloat(summary.tax || 0).toLocaleString()}</span>
                    </div>
                    <div class="flex justify-between text-lg font-semibold border-t pt-2">
                        <span class="text-gray-900">Total:</span>
                        <span class="text-gray-900">TZS ${parseFloat(summary.total || 0).toLocaleString()}</span>
                    </div>
                </div>
            `;

            document.getElementById('orderSummary').innerHTML = html;
        }

        async function placeOrder() {
            const form = document.getElementById('checkoutForm');
            const formData = new FormData(form);

            // Convert FormData to JSON for shipping_address
            const orderData = {
                shipping_address: {
                    full_name: formData.get('shipping_address[full_name]'),
                    phone: formData.get('shipping_address[phone]'),
                    street: formData.get('shipping_address[street]'),
                    city: formData.get('shipping_address[city]'),
                    state: formData.get('shipping_address[state]'),
                    postal_code: formData.get('shipping_address[postal_code]'),
                    country: formData.get('shipping_address[country]')
                },
                payment_method: formData.get('payment_method'),
                notes: formData.get('notes') || ''
            };

            try {
                const response = await fetch('/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(orderData)
                });

                const data = await response.json();

                if (data.success) {
                    // Show loading message
                    const button = document.querySelector('button[onclick="placeOrder()"]');
                    button.innerHTML =
                        '<i class="bi bi-arrow-clockwise animate-spin me-2"></i>Creating payment link...';
                    button.disabled = true;

                    // Redirect to payment or success page
                    if (data.order && data.order.id) {
                        // Create payment link if payment method is stripe
                        if (orderData.payment_method === 'stripe') {
                            createPaymentLink(data.order.id, data.order.final_amount || data.order.total_amount);
                        } else {
                            // Redirect to order confirmation for other payment methods
                            window.location.href = `/orders/${data.order.id}`;
                        }
                    }
                } else {
                    alert(data.message || 'Failed to place order');
                }
            } catch (error) {
                console.error('Error placing order:', error);
                alert('Failed to place order. Please try again.');
            }
        }

        async function createPaymentLink(orderId, amount) {
            try {
                // Show progress
                console.log('Creating payment link for order:', orderId, 'amount:', amount);

                const response = await fetch('/payments/create-link', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({
                        order_id: orderId,
                        amount: parseFloat(amount),
                        currency: 'tzs',
                        description: `Payment for Order #${orderId}`
                    })
                });

                const data = await response.json();
                console.log('Payment link response:', data);

                if (data.success && data.payment_link) {
                    // Show success message briefly
                    const button = document.querySelector('button[onclick="placeOrder()"]');
                    button.innerHTML = '<i class="bi bi-check-circle me-2"></i>Redirecting to payment...';

                    // Redirect immediately to Stripe checkout
                    setTimeout(() => {
                        window.location.href = data.payment_link;
                    }, 500); // Small delay to show the message
                } else {
                    // Reset button
                    const button = document.querySelector('button[onclick="placeOrder()"]');
                    button.innerHTML = '<i class="bi bi-check-circle me-2"></i>Place Order';
                    button.disabled = false;

                    alert(data.message || 'Failed to create payment link');
                }
            } catch (error) {
                console.error('Error creating payment link:', error);

                // Reset button
                const button = document.querySelector('button[onclick="placeOrder()"]');
                button.innerHTML = '<i class="bi bi-check-circle me-2"></i>Place Order';
                button.disabled = false;

                alert('Failed to create payment link. Please try again.');
            }
        }
    </script>
</body>

</html>
