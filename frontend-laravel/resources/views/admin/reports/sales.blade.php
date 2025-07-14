<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sales Reports - Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    <h1 class="text-3xl font-bold text-gray-900">Sales Reports</h1>
                    <p class="text-gray-600 mt-2">Analyze sales performance and trends</p>
                </div>
                <div class="flex items-center space-x-4">
                    <select class="border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                        <option>Last 30 days</option>
                        <option>Last 7 days</option>
                        <option>Last 3 months</option>
                        <option>Last year</option>
                        <option>Custom range</option>
                    </select>
                    <button class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">
                        <i class="bi bi-download me-2"></i>Export Report
                    </button>
                    <a href="{{ route('admin.dashboard') }}" 
                       class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                        <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Key Metrics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="bi bi-currency-dollar text-blue-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                        <p class="text-2xl font-bold text-gray-900">TZS 2,847,500</p>
                        <p class="text-sm text-green-600">+18.2% from last month</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full">
                        <i class="bi bi-box-seam text-green-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Orders</p>
                        <p class="text-2xl font-bold text-gray-900">1,847</p>
                        <p class="text-sm text-green-600">+12.5% from last month</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-full">
                        <i class="bi bi-graph-up text-purple-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Average Order Value</p>
                        <p class="text-2xl font-bold text-gray-900">TZS 1,542</p>
                        <p class="text-sm text-green-600">+5.1% from last month</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center">
                    <div class="bg-yellow-100 p-3 rounded-full">
                        <i class="bi bi-people text-yellow-600 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">New Customers</p>
                        <p class="text-2xl font-bold text-gray-900">234</p>
                        <p class="text-sm text-green-600">+8.7% from last month</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- Sales Chart -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Sales Trend</h3>
                    <div class="flex items-center space-x-2">
                        <button class="text-sm text-blue-600 hover:text-blue-800">Daily</button>
                        <button class="text-sm text-gray-500 hover:text-gray-700">Weekly</button>
                        <button class="text-sm text-gray-500 hover:text-gray-700">Monthly</button>
                    </div>
                </div>
                <div class="h-64">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            <!-- Top Products -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Top Selling Products</h3>
                    <a href="#" class="text-sm text-blue-600 hover:text-blue-800">View All</a>
                </div>
                <div class="space-y-4">
                    @for($i = 1; $i <= 5; $i++)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gray-200 rounded-lg flex items-center justify-center mr-3">
                                <i class="bi bi-box text-gray-500"></i>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Product {{ $i }}</p>
                                <p class="text-xs text-gray-500">{{ rand(50, 200) }} sold</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">TZS {{ number_format(rand(100000, 500000), 2) }}</p>
                            <p class="text-xs text-green-600">+{{ rand(5, 25) }}%</p>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Detailed Reports -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Sales by Category -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Sales by Category</h3>
                <div class="space-y-4">
                    @php
                        $categories = ['Electronics', 'Clothing', 'Home & Garden', 'Sports', 'Books'];
                        $colors = ['bg-blue-500', 'bg-green-500', 'bg-purple-500', 'bg-yellow-500', 'bg-red-500'];
                    @endphp
                    @foreach($categories as $index => $category)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <div class="w-3 h-3 {{ $colors[$index] }} rounded-full mr-3"></div>
                            <span class="text-sm text-gray-900">{{ $category }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-medium text-gray-900">{{ rand(15, 35) }}%</span>
                            <div class="w-16 bg-gray-200 rounded-full h-2 mt-1">
                                <div class="{{ $colors[$index] }} h-2 rounded-full" style="width: {{ rand(15, 35) }}%"></div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Customer Segments -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Customer Segments</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">New Customers</p>
                            <p class="text-xs text-gray-500">First-time buyers</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">234</p>
                            <p class="text-xs text-green-600">+8.7%</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">Returning Customers</p>
                            <p class="text-xs text-gray-500">Repeat buyers</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">1,613</p>
                            <p class="text-xs text-green-600">+12.3%</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-900">VIP Customers</p>
                            <p class="text-xs text-gray-500">High-value buyers</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">89</p>
                            <p class="text-xs text-green-600">+15.2%</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Methods -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-6">Payment Methods</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="bi bi-credit-card text-blue-600 mr-3"></i>
                            <span class="text-sm text-gray-900">Credit/Debit Cards</span>
                        </div>
                        <span class="text-sm font-medium text-gray-900">65%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="bi bi-phone text-green-600 mr-3"></i>
                            <span class="text-sm text-gray-900">Mobile Money</span>
                        </div>
                        <span class="text-sm font-medium text-gray-900">25%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="bi bi-bank text-purple-600 mr-3"></i>
                            <span class="text-sm text-gray-900">Bank Transfer</span>
                        </div>
                        <span class="text-sm font-medium text-gray-900">8%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <i class="bi bi-cash text-yellow-600 mr-3"></i>
                            <span class="text-sm text-gray-900">Cash on Delivery</span>
                        </div>
                        <span class="text-sm font-medium text-gray-900">2%</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Orders Table -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900">Recent High-Value Orders</h3>
                    <a href="{{ route('admin.orders.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View All Orders →
                    </a>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @for($i = 1; $i <= 8; $i++)
                        @php
                            $amount = rand(200000, 800000);
                            $statuses = ['delivered', 'shipped', 'confirmed', 'pending'];
                            $status = $statuses[array_rand($statuses)];
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">#ORD{{ str_pad($i, 4, '0', STR_PAD_LEFT) }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">Customer {{ $i }}</div>
                                <div class="text-sm text-gray-500">customer{{ $i }}@example.com</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                TZS {{ number_format($amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ rand(3, 8) }} items
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    @if($status === 'delivered') bg-green-100 text-green-800
                                    @elseif($status === 'shipped') bg-blue-100 text-blue-800
                                    @elseif($status === 'confirmed') bg-purple-100 text-purple-800
                                    @elseif($status === 'pending') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ date('M j, Y', strtotime('-' . rand(0, 7) . ' days')) }}
                            </td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Sales Chart
        const ctx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Sales (TZS)',
                    data: [180000, 220000, 190000, 280000, 320000, 290000, 350000, 380000, 340000, 420000, 390000, 450000],
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'TZS ' + (value / 1000) + 'K';
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>
