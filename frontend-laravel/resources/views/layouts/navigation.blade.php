<nav x-data="{ open: false, cartCount: 0, wishlistCount: 0 }" x-init="loadCounts()" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('products.index') }}" class="flex items-center">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                        <span class="ml-2 text-xl font-bold text-gray-800">E-Commerce</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                @if (App\Http\Controllers\AuthController::isAuthenticated())
                    @php
                        $user = App\Http\Controllers\AuthController::user();
                        $isAdmin = App\Http\Controllers\AuthController::isAdmin();
                    @endphp

                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        @if ($isAdmin)
                            <!-- Admin Navigation -->
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                                <i class="bi bi-speedometer2 me-1"></i>{{ __('Admin Dashboard') }}
                            </x-nav-link>
                        @else
                            <!-- User Navigation -->
                            <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                                <i class="bi bi-house me-1"></i>{{ __('Shop') }}
                            </x-nav-link>
                            <x-nav-link :href="route('orders.index')" :active="request()->routeIs('orders.*')">
                                <i class="bi bi-box-seam me-1"></i>{{ __('My Orders') }}
                            </x-nav-link>
                            <x-nav-link :href="route('wishlist.index')" :active="request()->routeIs('wishlist.*')">
                                <i class="bi bi-heart me-1"></i>{{ __('Wishlist') }}
                                <span x-show="wishlistCount > 0" x-text="wishlistCount"
                                    class="ml-1 bg-red-500 text-white text-xs rounded-full px-2 py-1"></span>
                            </x-nav-link>
                        @endif
                    </div>
                @else
                    <!-- Guest Navigation -->
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('products.index')" :active="request()->routeIs('products.*')">
                            <i class="bi bi-house me-1"></i>{{ __('Shop') }}
                        </x-nav-link>
                    </div>
                @endif
            </div>

            <!-- Right Side Navigation -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 space-x-4">
                @if (App\Http\Controllers\AuthController::isAuthenticated())
                    <!-- Cart Icon with Counter -->
                    <a href="{{ route('cart.index') }}" class="relative text-gray-500 hover:text-gray-700">
                        <i class="bi bi-cart text-xl"></i>
                        <span x-show="cartCount > 0" x-text="cartCount"
                            class="absolute -top-2 -right-2 bg-blue-500 text-white text-xs rounded-full px-2 py-1 min-w-[20px] text-center"></span>
                    </a>

                    <!-- Profile Dropdown -->
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                <div>
                                    {{ $user['full_name'] ?? ($user['email'] ?? 'User') }}
                                </div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            @if (!$isAdmin)
                                <x-dropdown-link :href="route('dashboard.user')">
                                    <i class="bi bi-speedometer2 me-2"></i>{{ __('Dashboard') }}
                                </x-dropdown-link>
                            @endif

                            <x-dropdown-link :href="route('profile.show')">
                                <i class="bi bi-person me-2"></i>{{ __('Profile') }}
                            </x-dropdown-link>

                            <x-dropdown-link :href="route('profile.edit')">
                                <i class="bi bi-pencil me-2"></i>{{ __('Edit Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();">
                                    <i class="bi bi-box-arrow-right me-2"></i>{{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <!-- Guest Links -->
                    <a href="{{ route('login') }}"
                        class="text-gray-500 hover:text-gray-700 px-3 py-2 text-sm font-medium">
                        {{ __('Login') }}
                    </a>
                    <a href="{{ route('register') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        {{ __('Register') }}
                    </a>
                @endif
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': !open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{ 'block': open, 'hidden': !open }" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">
                    {{ App\Http\Controllers\AuthController::user()['full_name'] ?? (App\Http\Controllers\AuthController::user()['email'] ?? 'User') }}
                </div>
                <div class="font-medium text-sm text-gray-500">
                    {{ App\Http\Controllers\AuthController::user()['email'] ?? 'user@example.com' }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<script>
    function loadCounts() {
        // Load cart count
        fetch('/cart/count', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.cartCount = data.count || 0;
                }
            })
            .catch(error => console.log('Cart count error:', error));

        // Load wishlist count
        fetch('/wishlist/count', {
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.wishlistCount = data.count || 0;
                }
            })
            .catch(error => console.log('Wishlist count error:', error));
    }

    // Update counts when items are added/removed
    window.updateNavCounts = function() {
        // Trigger Alpine.js to reload counts
        if (window.Alpine) {
            const nav = document.querySelector('[x-data]');
            if (nav && nav._x_dataStack && nav._x_dataStack[0]) {
                nav._x_dataStack[0].loadCounts();
            }
        }
    };
</script>
