<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Profile - E-Commerce</title>
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
                    <h1 class="text-3xl font-bold text-gray-900">My Profile</h1>
                    <p class="text-gray-600 mt-2">View and manage your account information</p>
                </div>
                <a href="{{ route('profile.edit') }}" 
                   class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    <i class="bi bi-pencil me-2"></i>Edit Profile
                </a>
            </div>
        </div>

        <!-- Profile Information -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Profile Card -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="text-center">
                        <!-- Profile Avatar -->
                        <div class="bg-blue-500 text-white rounded-full w-24 h-24 mx-auto mb-4 flex items-center justify-center text-2xl font-bold">
                            {{ strtoupper(substr($user['full_name'] ?? ($user['email'] ?? 'U'), 0, 2)) }}
                        </div>
                        
                        <!-- User Name -->
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">
                            {{ $user['full_name'] ?? 'User' }}
                        </h2>
                        
                        <!-- User Email -->
                        <p class="text-gray-600 mb-4">{{ $user['email'] ?? 'N/A' }}</p>
                        
                        <!-- User Role -->
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium
                            @if(($user['role'] ?? 'user') === 'admin') bg-purple-100 text-purple-800
                            @else bg-blue-100 text-blue-800 @endif">
                            <i class="bi bi-person-badge me-1"></i>
                            {{ ucfirst($user['role'] ?? 'user') }}
                        </span>
                        
                        <!-- Account Status -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <div class="flex items-center justify-center text-sm text-gray-600">
                                <i class="bi bi-check-circle text-green-500 me-2"></i>
                                Account Active
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-sm p-6 mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h3>
                    <div class="space-y-3">
                        <a href="{{ route('profile.edit') }}" 
                           class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                            <i class="bi bi-pencil me-3 text-blue-600"></i>
                            Edit Profile
                        </a>
                        <a href="{{ route('orders.index') }}" 
                           class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                            <i class="bi bi-box-seam me-3 text-green-600"></i>
                            My Orders
                        </a>
                        <a href="{{ route('wishlist.index') }}" 
                           class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                            <i class="bi bi-heart me-3 text-red-600"></i>
                            My Wishlist
                        </a>
                        <a href="{{ route('cart.index') }}" 
                           class="flex items-center w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-md transition-colors">
                            <i class="bi bi-cart me-3 text-purple-600"></i>
                            Shopping Cart
                        </a>
                    </div>
                </div>
            </div>

            <!-- Profile Details -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Personal Information -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Personal Information</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">
                                    {{ $user['full_name'] ?? 'Not provided' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">
                                    {{ $user['email'] ?? 'Not provided' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">
                                    {{ $user['phone'] ?? 'Not provided' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Date of Birth</label>
                                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">
                                    {{ isset($user['date_of_birth']) ? date('M j, Y', strtotime($user['date_of_birth'])) : 'Not provided' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Address Information -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Address Information</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Street Address</label>
                                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">
                                    {{ $user['address'] ?? 'Not provided' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">City</label>
                                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">
                                    {{ $user['city'] ?? 'Not provided' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">State/Region</label>
                                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">
                                    {{ $user['state'] ?? 'Not provided' }}
                                </p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Postal Code</label>
                                <p class="text-gray-900 bg-gray-50 px-3 py-2 rounded-md">
                                    {{ $user['postal_code'] ?? 'Not provided' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Statistics -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Account Statistics</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="text-center">
                                <div class="bg-blue-100 p-4 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                                    <i class="bi bi-box-seam text-blue-600 text-xl"></i>
                                </div>
                                <p class="text-2xl font-bold text-gray-900">{{ $orderCount ?? 0 }}</p>
                                <p class="text-sm text-gray-600">Total Orders</p>
                            </div>
                            <div class="text-center">
                                <div class="bg-green-100 p-4 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                                    <i class="bi bi-heart text-green-600 text-xl"></i>
                                </div>
                                <p class="text-2xl font-bold text-gray-900">{{ $wishlistCount ?? 0 }}</p>
                                <p class="text-sm text-gray-600">Wishlist Items</p>
                            </div>
                            <div class="text-center">
                                <div class="bg-purple-100 p-4 rounded-full w-16 h-16 mx-auto mb-3 flex items-center justify-center">
                                    <i class="bi bi-currency-dollar text-purple-600 text-xl"></i>
                                </div>
                                <p class="text-2xl font-bold text-gray-900">TZS {{ number_format($totalSpent ?? 0, 2) }}</p>
                                <p class="text-sm text-gray-600">Total Spent</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Account Settings -->
                <div class="bg-white rounded-lg shadow-sm">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">Account Settings</h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900">Email Notifications</h4>
                                    <p class="text-sm text-gray-600">Receive updates about your orders and promotions</p>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900">SMS Notifications</h4>
                                    <p class="text-sm text-gray-600">Get text messages about order updates</p>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900">Marketing Communications</h4>
                                    <p class="text-sm text-gray-600">Receive promotional offers and newsletters</p>
                                </div>
                                <div class="flex items-center">
                                    <input type="checkbox" checked class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 pt-6 border-t border-gray-200">
                            <div class="flex space-x-4">
                                <a href="{{ route('profile.edit') }}" 
                                   class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                                    <i class="bi bi-pencil me-2"></i>Edit Profile
                                </a>
                                <button class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                                    <i class="bi bi-key me-2"></i>Change Password
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
