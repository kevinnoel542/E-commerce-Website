# Important Files to Restore After Laravel Reinstall

## Controllers We Created:
1. **AuthController.php** - Custom FastAPI authentication integration
2. **ProductController.php** - Product management with FastAPI integration
3. **CartController.php** - Shopping cart functionality (session-based)
4. **WishlistController.php** - Wishlist functionality (session-based)
5. **OrderController.php** - Order management for users
6. **Admin/OrderController.php** - Admin order management

## Views We Created:
1. **layouts/app.blade.php** - Main application layout
2. **layouts/navigation.blade.php** - Navigation component
3. **dashboard.blade.php** - User dashboard
4. **admin/dashboard.blade.php** - Admin dashboard
5. **products/index.blade.php** - Product listing page
6. **products/show.blade.php** - Product detail page
7. **cart/index.blade.php** - Shopping cart page
8. **wishlist/index.blade.php** - Wishlist page
9. **orders/index.blade.php** - User orders page
10. **admin/orders/index.blade.php** - Admin orders management

## Routes We Created:
1. **web.php** - All our custom routes including:
   - Authentication routes (FastAPI integration)
   - Product routes
   - Cart routes (public access)
   - Wishlist routes (public access)
   - Order routes (authenticated)
   - Admin routes (authenticated + admin role)

## Key Features Implemented:
1. **FastAPI Integration** - Custom auth flow with JWT tokens
2. **Session-based Cart** - Works for guest and authenticated users
3. **Session-based Wishlist** - Works for guest and authenticated users
4. **Order Management** - Complete order processing system
5. **Admin Dashboard** - Order management and statistics
6. **Role-based Access** - User/Admin role separation
7. **CSRF Protection** - All forms properly protected
8. **Real-time Updates** - Cart/wishlist count updates
9. **Professional UI** - Tailwind CSS styling throughout

## Configuration Files:
1. **.env** - Environment configuration with FastAPI URLs
2. **tailwind.config.js** - Tailwind CSS configuration
3. **vite.config.js** - Vite build configuration
4. **package.json** - Node.js dependencies

## Database Integration:
- Uses FastAPI backend for all data operations
- No local database migrations needed
- Session storage for cart/wishlist

## Important Notes:
- Cart and wishlist use Laravel sessions (not database)
- All product data comes from FastAPI backend
- Authentication is handled by FastAPI with JWT tokens
- Admin features require 'admin' role in FastAPI backend
- CSRF tokens are properly handled in all AJAX requests
