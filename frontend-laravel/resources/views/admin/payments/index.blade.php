<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Payments Management - Admin</title>
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
                    <h1 class="text-3xl font-bold text-gray-900">Payments Management</h1>
                    <p class="text-gray-600 mt-2">Monitor and manage payment transactions</p>
                </div>
                <a href="{{ route('admin.dashboard') }}" 
                   class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                    <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Payment Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="bi bi-check-circle text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Successful Payments</p>
                        <p class="text-2xl font-bold text-gray-900">1,847</p>
                        <p class="text-sm text-green-600">+12% from last month</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="bi bi-clock text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Pending Payments</p>
                        <p class="text-2xl font-bold text-gray-900">23</p>
                        <p class="text-sm text-yellow-600">Needs attention</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-red-100 p-3 rounded-full">
                        <i class="bi bi-x-circle text-red-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Failed Payments</p>
                        <p class="text-2xl font-bold text-gray-900">89</p>
                        <p class="text-sm text-red-600">-5% from last month</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="bi bi-currency-dollar text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-900">TZS 2.4M</p>
                        <p class="text-sm text-blue-600">+18% from last month</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Tabs -->
        <div class="bg-white rounded-lg shadow-sm mb-6">
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8 px-6" aria-label="Tabs">
                    <a href="#" class="py-4 px-1 border-b-2 border-blue-500 font-medium text-sm text-blue-600">
                        All Payments
                        <span class="ml-2 bg-gray-100 text-gray-900 py-0.5 px-2.5 rounded-full text-xs">1,959</span>
                    </a>
                    <a href="#" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Successful
                        <span class="ml-2 bg-green-100 text-green-900 py-0.5 px-2.5 rounded-full text-xs">1,847</span>
                    </a>
                    <a href="#" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Pending
                        <span class="ml-2 bg-yellow-100 text-yellow-900 py-0.5 px-2.5 rounded-full text-xs">23</span>
                    </a>
                    <a href="#" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Failed
                        <span class="ml-2 bg-red-100 text-red-900 py-0.5 px-2.5 rounded-full text-xs">89</span>
                    </a>
                    <a href="#" class="py-4 px-1 border-b-2 border-transparent font-medium text-sm text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Refunds
                        <span class="ml-2 bg-purple-100 text-purple-900 py-0.5 px-2.5 rounded-full text-xs">12</span>
                    </a>
                </nav>
            </div>
        </div>

        <!-- Payments Table -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Payment Transactions</h3>
                    <div class="flex items-center space-x-4">
                        <!-- Date Filter -->
                        <select class="border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                            <option>Last 30 days</option>
                            <option>Last 7 days</option>
                            <option>Today</option>
                            <option>Custom range</option>
                        </select>
                        <!-- Search -->
                        <div class="relative">
                            <input type="text" placeholder="Search transactions..." 
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
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Sample Payment Transactions -->
                        @for($i = 1; $i <= 15; $i++)
                        @php
                            $statuses = ['paid', 'pending', 'failed', 'refunded'];
                            $methods = ['stripe', 'paypal', 'bank_transfer', 'mobile_money'];
                            $status = $statuses[array_rand($statuses)];
                            $method = $methods[array_rand($methods)];
                            $amount = rand(50000, 500000);
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-mono text-gray-900">TXN{{ str_pad($i, 6, '0', STR_PAD_LEFT) }}</div>
                                <div class="text-sm text-gray-500">{{ 'stripe_' . uniqid() }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">#ORD{{ str_pad($i, 4, '0', STR_PAD_LEFT) }}</div>
                                <div class="text-sm text-gray-500">{{ rand(1, 5) }} items</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">Customer {{ $i }}</div>
                                <div class="text-sm text-gray-500">customer{{ $i }}@example.com</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                TZS {{ number_format($amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    @if($method === 'stripe')
                                        <i class="bi bi-credit-card text-blue-600 mr-2"></i>
                                        <span class="text-sm text-gray-900">Stripe</span>
                                    @elseif($method === 'paypal')
                                        <i class="bi bi-paypal text-blue-600 mr-2"></i>
                                        <span class="text-sm text-gray-900">PayPal</span>
                                    @elseif($method === 'bank_transfer')
                                        <i class="bi bi-bank text-green-600 mr-2"></i>
                                        <span class="text-sm text-gray-900">Bank Transfer</span>
                                    @else
                                        <i class="bi bi-phone text-purple-600 mr-2"></i>
                                        <span class="text-sm text-gray-900">Mobile Money</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($status === 'paid') bg-green-100 text-green-800
                                    @elseif($status === 'pending') bg-yellow-100 text-yellow-800
                                    @elseif($status === 'failed') bg-red-100 text-red-800
                                    @elseif($status === 'refunded') bg-purple-100 text-purple-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ date('M j, Y', strtotime('-' . rand(0, 30) . ' days')) }}
                                <div class="text-xs text-gray-500">{{ date('g:i A', strtotime('-' . rand(0, 24) . ' hours')) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <button class="text-blue-600 hover:text-blue-900" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @if($status === 'paid')
                                        <button class="text-purple-600 hover:text-purple-900" title="Refund">
                                            <i class="bi bi-arrow-counterclockwise"></i>
                                        </button>
                                    @endif
                                    @if($status === 'pending')
                                        <button class="text-green-600 hover:text-green-900" title="Approve">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                        <button class="text-red-600 hover:text-red-900" title="Decline">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                    <button class="text-gray-600 hover:text-gray-900" title="Download Receipt">
                                        <i class="bi bi-download"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
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
                                Showing <span class="font-medium">1</span> to <span class="font-medium">15</span> of <span class="font-medium">1,959</span> results
                            </p>
                        </div>
                        <div>
                            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500">
                                    Page 1 of 131
                                </span>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
