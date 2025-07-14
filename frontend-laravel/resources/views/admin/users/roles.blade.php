<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>User Roles Management - Admin</title>
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
                    <h1 class="text-3xl font-bold text-gray-900">User Roles Management</h1>
                    <p class="text-gray-600 mt-2">Manage user roles and permissions</p>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.users.index') }}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                        <i class="bi bi-people me-2"></i>All Users
                    </a>
                    <a href="{{ route('admin.dashboard') }}" 
                       class="bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                        <i class="bi bi-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>

        <!-- Role Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Admin Role -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="bg-purple-100 p-3 rounded-full mr-4">
                            <i class="bi bi-shield-fill-check text-purple-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Admin</h3>
                            <p class="text-sm text-gray-600">Full system access</p>
                        </div>
                    </div>
                    <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full">5 users</span>
                </div>
                <div class="space-y-2 mb-4">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="bi bi-check-circle text-green-500 mr-2"></i>
                        Manage all orders
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="bi bi-check-circle text-green-500 mr-2"></i>
                        Manage products
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="bi bi-check-circle text-green-500 mr-2"></i>
                        Manage users
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="bi bi-check-circle text-green-500 mr-2"></i>
                        View reports
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="bi bi-check-circle text-green-500 mr-2"></i>
                        System settings
                    </div>
                </div>
                <button class="w-full bg-purple-600 text-white px-4 py-2 rounded-md hover:bg-purple-700 transition-colors">
                    Manage Admin Users
                </button>
            </div>

            <!-- User Role -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="bg-blue-100 p-3 rounded-full mr-4">
                            <i class="bi bi-person-fill text-blue-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">User</h3>
                            <p class="text-sm text-gray-600">Standard customer access</p>
                        </div>
                    </div>
                    <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full">1,229 users</span>
                </div>
                <div class="space-y-2 mb-4">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="bi bi-check-circle text-green-500 mr-2"></i>
                        Place orders
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="bi bi-check-circle text-green-500 mr-2"></i>
                        View order history
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="bi bi-check-circle text-green-500 mr-2"></i>
                        Manage profile
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="bi bi-check-circle text-green-500 mr-2"></i>
                        Wishlist access
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="bi bi-x-circle text-red-500 mr-2"></i>
                        Admin functions
                    </div>
                </div>
                <button class="w-full bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    Manage Regular Users
                </button>
            </div>

            <!-- Guest Role -->
            <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-gray-500">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <div class="bg-gray-100 p-3 rounded-full mr-4">
                            <i class="bi bi-person text-gray-600 text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Guest</h3>
                            <p class="text-sm text-gray-600">Limited browsing access</p>
                        </div>
                    </div>
                    <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded-full">No limit</span>
                </div>
                <div class="space-y-2 mb-4">
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="bi bi-check-circle text-green-500 mr-2"></i>
                        Browse products
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="bi bi-check-circle text-green-500 mr-2"></i>
                        View product details
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="bi bi-x-circle text-red-500 mr-2"></i>
                        Place orders
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="bi bi-x-circle text-red-500 mr-2"></i>
                        Access profile
                    </div>
                    <div class="flex items-center text-sm text-gray-600">
                        <i class="bi bi-x-circle text-red-500 mr-2"></i>
                        Save wishlist
                    </div>
                </div>
                <button class="w-full bg-gray-600 text-white px-4 py-2 rounded-md hover:bg-gray-700 transition-colors">
                    View Guest Activity
                </button>
            </div>
        </div>

        <!-- Role Assignment -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Role Assignment</h3>
                <p class="text-gray-600 mt-1">Change user roles and permissions</p>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Search User -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search User</label>
                        <div class="relative">
                            <input type="text" placeholder="Enter email or name..." 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center">
                                <i class="bi bi-search text-gray-400"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Assign Role -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Assign Role</label>
                        <div class="flex space-x-2">
                            <select class="flex-1 border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                                <option>Select Role</option>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            </select>
                            <button class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                                Assign
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Role Changes -->
        <div class="bg-white rounded-lg shadow-sm mt-6">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Recent Role Changes</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Previous Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">New Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Changed By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <!-- Sample Role Changes -->
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="bg-gray-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">
                                        JD
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">John Doe</div>
                                        <div class="text-sm text-gray-500">john@example.com</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    User
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-800">
                                    Admin
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Admin User
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ date('M j, Y g:i A', strtotime('-2 hours')) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-red-600 hover:text-red-900" title="Revert Change">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            </td>
                        </tr>
                        
                        @for($i = 1; $i <= 5; $i++)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="bg-gray-500 text-white rounded-full w-8 h-8 flex items-center justify-center text-sm font-bold mr-3">
                                        U{{ $i }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">User {{ $i }}</div>
                                        <div class="text-sm text-gray-500">user{{ $i }}@example.com</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    User
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    User
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                System
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ date('M j, Y g:i A', strtotime('-' . rand(1, 24) . ' hours')) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="text-red-600 hover:text-red-900" title="Revert Change">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                            </td>
                        </tr>
                        @endfor
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>
