<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
     <?php $__env->slot('header', null, []); ?> 
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            <?php echo e(__('Products')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Products Header -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Products</h1>
                        <p class="text-gray-600">Discover our amazing products</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-4">
                        <!-- Category Filter -->
                        <select id="categoryFilter" onchange="filterProducts()"
                            class="border border-gray-300 rounded-lg px-4 py-2 text-sm">
                            <option value="">All Categories</option>
                            <?php if(isset($categories) && count($categories) > 0): ?>
                                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($category['id']); ?>"><?php echo e($category['name']); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        </select>

                        <!-- Sort Filter -->
                        <select id="sortFilter" onchange="filterProducts()"
                            class="border border-gray-300 rounded-lg px-4 py-2 text-sm">
                            <option value="">Sort by: Featured</option>
                            <option value="price_asc">Price: Low to High</option>
                            <option value="price_desc">Price: High to Low</option>
                            <option value="newest">Newest First</option>
                            <option value="name_asc">Name: A to Z</option>
                        </select>

                        <!-- Price Range -->
                        <div class="flex items-center space-x-2">
                            <input type="number" id="minPrice" placeholder="Min $" onchange="filterProducts()"
                                class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-20">
                            <span class="text-gray-500">-</span>
                            <input type="number" id="maxPrice" placeholder="Max $" onchange="filterProducts()"
                                class="border border-gray-300 rounded-lg px-3 py-2 text-sm w-20">
                        </div>

                        <!-- Stock Filter -->
                        <label class="flex items-center space-x-2">
                            <input type="checkbox" id="inStockOnly" onchange="filterProducts()" class="rounded">
                            <span class="text-sm text-gray-700">In Stock Only</span>
                        </label>

                        <!-- Search Input -->
                        <div class="flex-1 min-w-64">
                            <div class="relative">
                                <input type="text" id="searchInput" placeholder="Search products..."
                                    onkeyup="searchProductsDebounced()"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm pr-10">
                                <button onclick="clearSearch()"
                                    class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <i class="bi bi-x-circle"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Advanced Search Toggle -->
                        <button onclick="toggleAdvancedSearch()"
                            class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-200 transition">
                            <i class="bi bi-funnel mr-2"></i>Advanced
                        </button>
                    </div>

                    <!-- Advanced Search Panel -->
                    <div id="advancedSearch" class="hidden mt-4 p-4 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Brand</label>
                                <input type="text" id="brandFilter" placeholder="Enter brand name"
                                    onchange="filterProducts()"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
                                <input type="text" id="skuFilter" placeholder="Enter SKU" onchange="filterProducts()"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Results per page</label>
                                <select id="limitFilter" onchange="filterProducts()"
                                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm">
                                    <option value="12">12 products</option>
                                    <option value="24">24 products</option>
                                    <option value="48">48 products</option>
                                    <option value="96">96 products</option>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end space-x-2">
                            <button onclick="clearAllFilters()"
                                class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-400 transition">
                                Clear All
                            </button>
                            <button onclick="filterProducts()"
                                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition">
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Results Counter -->
            <div class="flex justify-between items-center mb-6">
                <p class="text-sm text-gray-600" id="results-count">
                    <?php if(isset($products) && count($products) > 0): ?>
                        <?php echo e(count($products)); ?> product<?php echo e(count($products) !== 1 ? 's' : ''); ?> found
                    <?php else: ?>
                        Loading products...
                    <?php endif; ?>
                </p>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-600">View:</span>
                    <button onclick="toggleView('grid')" id="gridViewBtn"
                        class="p-2 text-gray-600 hover:text-blue-600 transition">
                        <i class="bi bi-grid-3x3-gap"></i>
                    </button>
                    <button onclick="toggleView('list')" id="listViewBtn"
                        class="p-2 text-gray-600 hover:text-blue-600 transition">
                        <i class="bi bi-list"></i>
                    </button>
                </div>
            </div>

            <!-- Products Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="products-grid">
                <?php if(isset($products) && count($products) > 0): ?>
                    <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div
                            class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                            <div
                                class="aspect-square bg-gray-100 rounded-t-xl flex items-center justify-center overflow-hidden">
                                <?php if(isset($product['images']) && is_array($product['images']) && count($product['images']) > 0): ?>
                                    <?php
                                        $imageUrl = $product['images'][0];
                                        // Handle both old and new image URL formats
                                        if (strpos($imageUrl, 'http') !== 0) {
                                            $imageUrl = asset('storage' . $imageUrl);
                                        }
                                    ?>
                                    <img src="<?php echo e($imageUrl); ?>" alt="<?php echo e($product['name']); ?>"
                                        class="w-full h-full object-cover">
                                <?php else: ?>
                                    <span class="text-4xl">📦</span>
                                <?php endif; ?>
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 mb-2"><?php echo e($product['name'] ?? 'Product Name'); ?>

                                </h3>
                                <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                                    <?php echo e($product['description'] ?? 'No description available.'); ?></p>
                                <div class="flex items-center justify-between mb-3">
                                    <span
                                        class="text-lg font-bold text-green-600">$<?php echo e(number_format($product['price'] ?? 0, 2)); ?></span>
                                    <?php if(($product['stock_quantity'] ?? 0) > 0): ?>
                                        <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full">In
                                            Stock</span>
                                    <?php else: ?>
                                        <span class="text-xs text-red-600 bg-red-100 px-2 py-1 rounded-full">Out of
                                            Stock</span>
                                    <?php endif; ?>
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="addToCart(<?php echo e($product['id']); ?>)"
                                        class="flex-1 bg-blue-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-blue-700 transition <?php echo e(($product['stock_quantity'] ?? 0) <= 0 ? 'opacity-50 cursor-not-allowed' : ''); ?>"
                                        <?php echo e(($product['stock_quantity'] ?? 0) <= 0 ? 'disabled' : ''); ?>>
                                        <i class="bi bi-cart-plus mr-1"></i>Add to Cart
                                    </button>
                                    <button onclick="addToWishlist(<?php echo e($product['id']); ?>)"
                                        class="bg-gray-100 text-gray-600 px-3 py-2 rounded-lg text-sm hover:bg-gray-200 transition">
                                        <i class="bi bi-heart"></i>
                                    </button>
                                    <a href="<?php echo e(route('products.show', $product['id'])); ?>"
                                        class="bg-gray-100 text-gray-600 px-3 py-2 rounded-lg text-sm hover:bg-gray-200 transition">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <div class="col-span-full text-center py-12 text-gray-500">
                        <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"
                            id="loading-spinner"></div>
                        <p class="text-lg" id="loading-text">Loading products...</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            <?php if(!isset($products) || count($products) == 0): ?>
                loadProducts();
            <?php endif; ?>
        });

        async function loadProducts() {
            try {
                const response = await fetch('/api/products');
                const data = await response.json();

                if (data && data.length > 0) {
                    renderProducts(data);
                } else {
                    showNoProducts();
                }
            } catch (error) {
                console.error('Error loading products:', error);
                showNoProducts();
            }
        }

        function renderProducts(products, viewType = 'grid') {
            const grid = document.getElementById('products-grid');
            let html = '';

            // Store current products for view switching
            window.currentProducts = products;

            products.forEach(product => {
                const inStock = (product.stock_quantity || 0) > 0;
                let imageUrl = (product.images && product.images.length > 0) ? product.images[0] : '';

                // Handle both old and new image URL formats
                if (imageUrl && !imageUrl.startsWith('http')) {
                    imageUrl = '<?php echo e(asset('storage')); ?>' + imageUrl;
                }

                if (viewType === 'list') {
                    // List view layout
                    html += `
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                            <div class="flex p-4">
                                <div class="w-24 h-24 bg-gray-100 rounded-lg flex items-center justify-center overflow-hidden mr-4">
                                    ${imageUrl ?
                                        `<img src="${imageUrl}" alt="${product.name}" class="w-full h-full object-cover">` :
                                        `<span class="text-2xl">📦</span>`
                                    }
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-gray-900 mb-1">${product.name || 'Product Name'}</h3>
                                            <p class="text-sm text-gray-600 mb-2 line-clamp-2">${product.description || 'No description available.'}</p>
                                            <div class="flex items-center space-x-4">
                                                <span class="text-lg font-bold text-green-600">$${(product.price || 0).toFixed(2)}</span>
                                                ${inStock ?
                                                    `<span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full">In Stock</span>` :
                                                    `<span class="text-xs text-red-600 bg-red-100 px-2 py-1 rounded-full">Out of Stock</span>`
                                                }
                                            </div>
                                        </div>
                                        <div class="flex space-x-2 ml-4">
                                            <button onclick="addToCart(${product.id})"
                                                class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition ${!inStock ? 'opacity-50 cursor-not-allowed' : ''}"
                                                ${!inStock ? 'disabled' : ''}>
                                                <i class="bi bi-cart-plus mr-1"></i>Add to Cart
                                            </button>
                                            <button onclick="addToWishlist(${product.id})" class="bg-gray-100 text-gray-600 px-3 py-2 rounded-lg text-sm hover:bg-gray-200 transition">
                                                <i class="bi bi-heart"></i>
                                            </button>
                                            <a href="/products/${product.id}" class="bg-gray-100 text-gray-600 px-3 py-2 rounded-lg text-sm hover:bg-gray-200 transition">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    // Grid view layout (default)
                    html += `
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                            <div class="aspect-square bg-gray-100 rounded-t-xl flex items-center justify-center overflow-hidden">
                                ${imageUrl ?
                                    `<img src="${imageUrl}" alt="${product.name}" class="w-full h-full object-cover">` :
                                    `<span class="text-4xl">📦</span>`
                                }
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 mb-2">${product.name || 'Product Name'}</h3>
                                <p class="text-sm text-gray-600 mb-3 line-clamp-2">${product.description || 'No description available.'}</p>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="text-lg font-bold text-green-600">$${(product.price || 0).toFixed(2)}</span>
                                    ${inStock ?
                                        `<span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full">In Stock</span>` :
                                        `<span class="text-xs text-red-600 bg-red-100 px-2 py-1 rounded-full">Out of Stock</span>`
                                    }
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="addToCart(${product.id})"
                                        class="flex-1 bg-blue-600 text-white px-3 py-2 rounded-lg text-sm hover:bg-blue-700 transition ${!inStock ? 'opacity-50 cursor-not-allowed' : ''}"
                                        ${!inStock ? 'disabled' : ''}>
                                        <i class="bi bi-cart-plus mr-1"></i>Add to Cart
                                    </button>
                                    <button onclick="addToWishlist(${product.id})" class="bg-gray-100 text-gray-600 px-3 py-2 rounded-lg text-sm hover:bg-gray-200 transition">
                                        <i class="bi bi-heart"></i>
                                    </button>
                                    <a href="/products/${product.id}" class="bg-gray-100 text-gray-600 px-3 py-2 rounded-lg text-sm hover:bg-gray-200 transition">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                }
            });

            grid.innerHTML = html;
        }

        function showNoProducts() {
            document.getElementById('products-grid').innerHTML = `
                <div class="col-span-full bg-white rounded-xl shadow-sm border border-gray-100 p-12">
                    <div class="text-center text-gray-500">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <span class="text-4xl">🛍️</span>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">No products available</h3>
                        <p class="text-gray-600 mb-6">No products found. Try adjusting your filters or check back later!</p>
                    </div>
                </div>
            `;
        }

        async function filterProducts() {
            const category = document.getElementById('categoryFilter').value;
            const sort = document.getElementById('sortFilter').value;
            const search = document.getElementById('searchInput').value;
            const minPrice = document.getElementById('minPrice').value;
            const maxPrice = document.getElementById('maxPrice').value;
            const inStockOnly = document.getElementById('inStockOnly').checked;
            const brand = document.getElementById('brandFilter').value;
            const sku = document.getElementById('skuFilter').value;
            const limit = document.getElementById('limitFilter').value;

            const params = new URLSearchParams();
            if (category) params.append('category', category);
            if (sort) params.append('sort', sort);
            if (search) params.append('search', search);
            if (minPrice) params.append('min_price', minPrice);
            if (maxPrice) params.append('max_price', maxPrice);
            if (inStockOnly) params.append('in_stock', 'true');
            if (brand) params.append('brand', brand);
            if (sku) params.append('sku', sku);
            if (limit) params.append('limit', limit);

            try {
                showLoading();
                const response = await fetch(`/api/products?${params.toString()}`);
                const data = await response.json();

                if (data && data.length > 0) {
                    renderProducts(data);
                    updateResultsCount(data.length);
                } else {
                    showNoProducts();
                    updateResultsCount(0);
                }
            } catch (error) {
                console.error('Error filtering products:', error);
                showNoProducts();
                updateResultsCount(0);
            }
        }

        async function searchProducts() {
            const searchTerm = document.getElementById('searchInput').value;

            if (searchTerm.length < 2) {
                if (searchTerm.length === 0) {
                    filterProducts(); // Reset to all products
                }
                return;
            }

            const params = new URLSearchParams();
            params.append('q', searchTerm);

            // Include other active filters
            const category = document.getElementById('categoryFilter').value;
            const minPrice = document.getElementById('minPrice').value;
            const maxPrice = document.getElementById('maxPrice').value;
            const limit = document.getElementById('limitFilter').value;

            if (category) params.append('category', category);
            if (minPrice) params.append('min_price', minPrice);
            if (maxPrice) params.append('max_price', maxPrice);
            if (limit) params.append('limit', limit);

            try {
                showLoading();
                const response = await fetch(`/api/products/search?${params.toString()}`);
                const data = await response.json();

                if (data && data.length > 0) {
                    renderProducts(data);
                    updateResultsCount(data.length);
                } else {
                    showNoProducts();
                    updateResultsCount(0);
                }
            } catch (error) {
                console.error('Error searching products:', error);
                showNoProducts();
                updateResultsCount(0);
            }
        }

        let searchTimeout;

        function searchProductsDebounced() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(searchProducts, 500); // Debounce search
        }

        function toggleAdvancedSearch() {
            const panel = document.getElementById('advancedSearch');
            const button = event.target.closest('button');

            if (panel.classList.contains('hidden')) {
                panel.classList.remove('hidden');
                button.innerHTML = '<i class="bi bi-funnel-fill mr-2"></i>Hide Advanced';
            } else {
                panel.classList.add('hidden');
                button.innerHTML = '<i class="bi bi-funnel mr-2"></i>Advanced';
            }
        }

        function clearSearch() {
            document.getElementById('searchInput').value = '';
            filterProducts();
        }

        function clearAllFilters() {
            document.getElementById('categoryFilter').value = '';
            document.getElementById('sortFilter').value = '';
            document.getElementById('searchInput').value = '';
            document.getElementById('minPrice').value = '';
            document.getElementById('maxPrice').value = '';
            document.getElementById('inStockOnly').checked = false;
            document.getElementById('brandFilter').value = '';
            document.getElementById('skuFilter').value = '';
            document.getElementById('limitFilter').value = '12';

            filterProducts();
        }

        function showLoading() {
            const grid = document.getElementById('products-grid');
            grid.innerHTML = `
                <div class="col-span-full text-center py-12 text-gray-500">
                    <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600 mx-auto mb-4"></div>
                    <p class="text-lg">Loading products...</p>
                </div>
            `;
        }

        function updateResultsCount(count) {
            // Update results count if there's a results counter element
            const counter = document.getElementById('results-count');
            if (counter) {
                counter.textContent = `${count} product${count !== 1 ? 's' : ''} found`;
            }
        }

        function toggleView(viewType) {
            const grid = document.getElementById('products-grid');
            const gridBtn = document.getElementById('gridViewBtn');
            const listBtn = document.getElementById('listViewBtn');

            if (viewType === 'grid') {
                grid.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6';
                gridBtn.classList.add('text-blue-600');
                gridBtn.classList.remove('text-gray-600');
                listBtn.classList.add('text-gray-600');
                listBtn.classList.remove('text-blue-600');
            } else {
                grid.className = 'space-y-4';
                listBtn.classList.add('text-blue-600');
                listBtn.classList.remove('text-gray-600');
                gridBtn.classList.add('text-gray-600');
                gridBtn.classList.remove('text-blue-600');
            }

            // Re-render products with new view
            const currentProducts = window.currentProducts || [];
            if (currentProducts.length > 0) {
                renderProducts(currentProducts, viewType);
            }
        }

        async function addToCart(productId) {
            try {
                const response = await fetch('/cart/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        product_id: productId,
                        quantity: 1
                    })
                });

                const data = await response.json();
                if (data.success) {
                    showMessage(data.message || 'Product added to cart successfully!', 'success');
                    updateCartCount();
                } else {
                    showMessage(data.message || 'Failed to add product to cart', 'error');
                }
            } catch (error) {
                console.error('Error adding to cart:', error);
                showMessage('Failed to add product to cart', 'error');
            }
        }

        async function updateCartCount() {
            try {
                const response = await fetch('/cart/count');
                const data = await response.json();

                // Update cart count in navigation if it exists
                const cartCountElement = document.querySelector('.cart-count');
                if (cartCountElement) {
                    cartCountElement.textContent = data.count;
                }
            } catch (error) {
                console.error('Error updating cart count:', error);
            }
        }

        async function addToWishlist(productId) {
            try {
                const response = await fetch('/wishlist/add', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content')
                    },
                    body: JSON.stringify({
                        product_id: productId
                    })
                });

                const data = await response.json();
                if (data.success) {
                    showMessage(data.message || 'Product added to wishlist successfully!', 'success');
                    updateWishlistCount();

                    // Change heart icon color to red to indicate it's in wishlist
                    const heartButtons = document.querySelectorAll(`button[onclick="addToWishlist(${productId})"]`);
                    heartButtons.forEach(button => {
                        const heartIcon = button.querySelector('i');
                        if (heartIcon) {
                            heartIcon.style.color = '#ef4444'; // red-500
                            button.title = 'Added to wishlist';
                        }
                    });
                } else {
                    showMessage(data.message || 'Failed to add product to wishlist', 'error');
                }
            } catch (error) {
                console.error('Error adding to wishlist:', error);
                showMessage('Failed to add product to wishlist', 'error');
            }
        }

        async function updateWishlistCount() {
            try {
                const response = await fetch('/wishlist/count');
                const data = await response.json();

                // Update wishlist count in navigation if it exists
                const wishlistCountElement = document.querySelector('.wishlist-count');
                if (wishlistCountElement) {
                    wishlistCountElement.textContent = data.count;
                }
            } catch (error) {
                console.error('Error updating wishlist count:', error);
            }
        }

        function showMessage(message, type) {
            // Remove existing messages
            const existingMessages = document.querySelectorAll('.alert-message');
            existingMessages.forEach(msg => msg.remove());

            const alertClass = type === 'success' ? 'bg-green-100 border-green-400 text-green-700' :
                'bg-red-100 border-red-400 text-red-700';

            const messageDiv = document.createElement('div');
            messageDiv.className = `alert-message fixed top-4 right-4 z-50 border px-4 py-3 rounded ${alertClass}`;
            messageDiv.innerHTML = `
                <span class="block sm:inline">${message}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentElement.remove()">
                    <svg class="fill-current h-6 w-6" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <title>Close</title>
                        <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                    </svg>
                </span>
            `;

            document.body.appendChild(messageDiv);

            // Auto-remove after 5 seconds
            setTimeout(() => {
                if (messageDiv.parentElement) {
                    messageDiv.remove();
                }
            }, 5000);
        }
    </script>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH C:\Users\HACKER JOE\Desktop\MAIN FOLDER\E-commerce FASTAPI AND LARAVEL\frontend-laravel\resources\views/products/index.blade.php ENDPATH**/ ?>