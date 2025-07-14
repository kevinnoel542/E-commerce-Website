<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminProductController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Public Routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Cart routes (public - work for both authenticated and guest users)
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::post('/update', [CartController::class, 'update'])->name('update');
    Route::post('/remove', [CartController::class, 'remove'])->name('remove');
    Route::post('/clear', [CartController::class, 'clear'])->name('clear');
    Route::get('/count', [CartController::class, 'count'])->name('count');
    Route::get('/summary', [CartController::class, 'summary'])->name('summary');
});

// Wishlist routes (public - work for both authenticated and guest users)
Route::prefix('wishlist')->name('wishlist.')->group(function () {
    Route::get('/', [WishlistController::class, 'index'])->name('index');
    Route::post('/add', [WishlistController::class, 'add'])->name('add');
    Route::post('/remove', [WishlistController::class, 'remove'])->name('remove');
    Route::post('/toggle', [WishlistController::class, 'toggle'])->name('toggle');
    Route::post('/move-to-cart', [WishlistController::class, 'moveToCart'])->name('move-to-cart');
    Route::post('/clear', [WishlistController::class, 'clear'])->name('clear');
    Route::get('/count', [WishlistController::class, 'count'])->name('count');
    Route::post('/check', [WishlistController::class, 'check'])->name('check');
});

// Payment callback routes (public - no auth required)
Route::get('/payment-success', [PaymentController::class, 'paymentSuccess'])->name('payment.success');
Route::get('/payment-cancel', [PaymentController::class, 'paymentCancel'])->name('payment.cancel');

// Public Product routes (no authentication required)
Route::prefix('products')->name('products.')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('/search', [ProductController::class, 'search'])->name('search');
    Route::get('/categories', [ProductController::class, 'categories'])->name('categories');
    Route::get('/featured', [ProductController::class, 'featured'])->name('featured');
    Route::get('/{id}', [ProductController::class, 'show'])->name('show');
    Route::get('/{id}/recommendations', [ProductController::class, 'recommendations'])->name('recommendations');
});

// Authentication Processing Routes (Custom FastAPI Integration)
Route::middleware('guest')->group(function () {
    // Auth processing routes (views are handled by auth.php)
    Route::post('/auth/login', [AuthController::class, 'login'])->name('auth.login');
});

// Logout routes (available to authenticated users)
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('custom.auth')
    ->name('logout');

Route::get('/logout', [AuthController::class, 'logout'])
    ->middleware('custom.auth')
    ->name('logout.get');

// Protected User Routes
Route::middleware(['custom.auth'])->group(function () {
    // User Dashboard
    Route::get('/dashboard/user', [DashboardController::class, 'userDashboard'])->name('dashboard.user');

    // Default dashboard redirect based on role
    Route::get('/dashboard', function () {
        if (App\Http\Controllers\AuthController::isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('dashboard.user');
    })->name('dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Password Routes
    Route::put('/password/update', [ProfileController::class, 'updatePassword'])->name('password.update');

    // Payment Routes
    Route::post('/payments/create-link', [PaymentController::class, 'createPaymentLink'])->name('payments.create-link');
    Route::post('/payments/verify', [PaymentController::class, 'verifyPayment'])->name('payments.verify');

    // Email verification route (placeholder for now)
    Route::post('/email/verification-notification', function () {
        return redirect()->back()->with('status', 'verification-link-sent');
    })->name('verification.send');

    // Checkout routes (authenticated users only)
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');

    // Order routes (authenticated users only)
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::get('/', [OrderController::class, 'index'])->name('index');
        Route::get('/{id}', [OrderController::class, 'show'])->name('show');
        Route::post('/', [OrderController::class, 'create'])->name('create');
        Route::post('/cart/summary', [OrderController::class, 'cartSummary'])->name('cart.summary');
        Route::post('/{id}/payment', [OrderController::class, 'payment'])->name('payment');
    });
});

// Protected Admin Routes
Route::middleware(['custom.admin'])->group(function () {
    // Admin Dashboard
    Route::get('/dashboard/admin', [DashboardController::class, 'adminDashboard'])->name('admin.dashboard');

    // Product management routes
    Route::prefix('dashboard/admin/products')->name('admin.products.')->group(function () {
        Route::get('/', [AdminProductController::class, 'index'])->name('index');
        Route::get('/create', [AdminProductController::class, 'create'])->name('create');
        Route::post('/', [AdminProductController::class, 'store'])->name('store');
        Route::post('/upload-image', [AdminProductController::class, 'uploadImage'])->name('upload-image');
        Route::get('/{id}', [AdminProductController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [AdminProductController::class, 'edit'])->name('edit');
        Route::patch('/{id}', [AdminProductController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminProductController::class, 'destroy'])->name('destroy');
    });

    // Order management routes
    Route::prefix('dashboard/admin/orders')->name('admin.orders.')->group(function () {
        Route::get('/', [OrderController::class, 'adminIndex'])->name('index');
        Route::get('/{id}', [OrderController::class, 'show'])->name('show');
        Route::patch('/{id}/status', [OrderController::class, 'updateStatus'])->name('update-status');
        Route::patch('/{id}/payment-status', [OrderController::class, 'updatePaymentStatus'])->name('update-payment-status');
    });

    // User management routes
    Route::prefix('dashboard/admin/users')->name('admin.users.')->group(function () {
        Route::get('/', function () {
            return view('admin.users.index');
        })->name('index');
        Route::get('/roles', function () {
            return view('admin.users.roles');
        })->name('roles');
    });

    // Payment management routes
    Route::prefix('dashboard/admin/payments')->name('admin.payments.')->group(function () {
        Route::get('/', function () {
            return view('admin.payments.index');
        })->name('index');
    });

    // Reports routes
    Route::prefix('dashboard/admin/reports')->name('admin.reports.')->group(function () {
        Route::get('/sales', function () {
            return view('admin.reports.sales');
        })->name('sales');
        Route::get('/customers', function () {
            return view('admin.reports.customers');
        })->name('customers');
        Route::get('/inventory', function () {
            return view('admin.reports.inventory');
        })->name('inventory');
    });

    // Debug route for testing product creation
    Route::get('/debug/test-product-creation', function () {
        $token = Session::get('jwt_token');
        $user = Session::get('user');
        $userRole = Session::get('user_role');
        $userData = Session::get('user_data');

        return response()->json([
            'token_exists' => !empty($token),
            'token_preview' => $token ? substr($token, 0, 20) . '...' : null,
            'user' => $user,
            'user_role' => $userRole,
            'user_data' => $userData,
            'is_admin' => AuthController::isAdmin(),
            'is_authenticated' => AuthController::isAuthenticated(),
            'fastapi_url' => env('FASTAPI_URL', 'http://localhost:8000/api/v1'),
            'all_session_data' => Session::all()
        ]);
    })->name('debug.test-product-creation');

    // Test product creation route
    Route::post('/debug/create-test-product', function () {
        $token = Session::get('jwt_token');

        if (!$token) {
            return response()->json(['error' => 'No token found'], 401);
        }

        $testData = [
            'name' => 'Debug Test Product',
            'description' => 'This is a debug test product',
            'price' => 19.99,
            'category_id' => null,
            'brand' => 'Debug Brand',
            'stock_quantity' => 5,
            'images' => []
        ];

        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Authorization' => "Bearer {$token}",
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json'
                ])
                ->post('http://localhost:8000/api/v1/products/', $testData);

            return response()->json([
                'status' => $response->status(),
                'success' => $response->successful(),
                'body' => $response->json(),
                'sent_data' => $testData
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'sent_data' => $testData
            ], 500);
        }
    })->name('debug.create-test-product');

    // Simple test route to check admin access
    Route::get('/debug/admin-check', function () {
        return response()->json([
            'is_authenticated' => AuthController::isAuthenticated(),
            'is_admin' => AuthController::isAdmin(),
            'user_role' => AuthController::userRole(),
            'user_data' => Session::get('user_data'),
            'user' => Session::get('user'),
            'admin_middleware_would_pass' => AuthController::isAdmin() || (Session::get('user_data')['email'] ?? '') === 'starkalyboy@gmail.com'
        ]);
    })->name('debug.admin-check');
});

// Include auth routes for login/register views
require __DIR__ . '/auth.php';
