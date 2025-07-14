<?php

return [
    /*
    |--------------------------------------------------------------------------
    | FastAPI Backend Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains configuration for connecting to the FastAPI backend.
    | These settings are used throughout the Laravel application to make
    | API calls to the FastAPI backend.
    |
    */

    'base_url' => env('FASTAPI_BASE_URL', 'http://localhost:8000'),
    'api_url' => env('FASTAPI_API_URL', 'http://localhost:8000/api/v1'),
    'url' => env('FASTAPI_URL', 'http://localhost:8000/api/v1'),
    
    'timeout' => env('FASTAPI_TIMEOUT', 30),
    
    'endpoints' => [
        'auth' => [
            'login' => '/auth/login',
            'register' => '/auth/register',
            'logout' => '/auth/logout',
            'refresh' => '/auth/refresh',
            'profile' => '/auth/profile',
        ],
        'products' => [
            'list' => '/products/',
            'create' => '/products/',
            'show' => '/products/{id}',
            'update' => '/products/{id}',
            'delete' => '/products/{id}',
            'upload_image' => '/products/upload-image',
            'categories' => '/products/categories/',
        ],
        'orders' => [
            'list' => '/orders/',
            'create' => '/orders/',
            'show' => '/orders/{id}',
            'cart_summary' => '/orders/cart/summary',
            'payment' => '/orders/{id}/payment',
        ],
        'payments' => [
            'stripe_checkout' => '/stripe/create-checkout-session',
            'stripe_verify' => '/stripe/verify/{session_id}',
            'stripe_status' => '/stripe/status/{session_id}',
        ],
    ],
];
