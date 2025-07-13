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
            <?php echo e(__('Product API Testing')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Test Results Display -->
            <div id="test-results" class="mb-8 space-y-4"></div>

            <!-- Test Controls -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- GET Tests -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📥 GET Operations</h3>

                    <div class="space-y-4">
                        <!-- Test 1: List Products -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">1. List Products (GET /api/v1/products/)</h4>
                            <div class="flex space-x-2 mb-2">
                                <input type="number" id="limit" placeholder="Limit"
                                    class="border rounded px-2 py-1 text-sm" value="10">
                                <input type="number" id="offset" placeholder="Offset"
                                    class="border rounded px-2 py-1 text-sm" value="0">
                                <select id="sort" class="border rounded px-2 py-1 text-sm">
                                    <option value="">No Sort</option>
                                    <option value="price_asc">Price: Low to High</option>
                                    <option value="price_desc">Price: High to Low</option>
                                    <option value="name_asc">Name: A to Z</option>
                                    <option value="newest">Newest First</option>
                                </select>
                            </div>
                            <button onclick="testListProducts()"
                                class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                                Test List Products
                            </button>
                        </div>

                        <!-- Test 2: Search Products -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">2. Search Products (GET /api/v1/products/search)
                            </h4>
                            <div class="flex space-x-2 mb-2">
                                <input type="text" id="search-query" placeholder="Search query"
                                    class="border rounded px-2 py-1 text-sm flex-1" value="laptop">
                                <input type="number" id="search-limit" placeholder="Limit"
                                    class="border rounded px-2 py-1 text-sm" value="5">
                            </div>
                            <button onclick="testSearchProducts()"
                                class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">
                                Test Search Products
                            </button>
                        </div>

                        <!-- Test 3: Get Product Details -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">3. Get Product Details (GET
                                /api/v1/products/{id})</h4>
                            <div class="flex space-x-2 mb-2">
                                <input type="text" id="product-id" placeholder="Product ID"
                                    class="border rounded px-2 py-1 text-sm flex-1">
                                <button onclick="getFirstProductId()"
                                    class="bg-gray-500 text-white px-3 py-1 rounded text-sm">Get First ID</button>
                            </div>
                            <button onclick="testGetProduct()"
                                class="bg-purple-600 text-white px-4 py-2 rounded text-sm hover:bg-purple-700">
                                Test Get Product
                            </button>
                        </div>

                        <!-- Test 4: Get Categories -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">4. Get Categories</h4>
                            <button onclick="testGetCategories()"
                                class="bg-indigo-600 text-white px-4 py-2 rounded text-sm hover:bg-indigo-700">
                                Test Get Categories
                            </button>
                        </div>
                    </div>
                </div>

                <!-- POST/PUT/DELETE Tests -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">📤 POST/PUT/DELETE Operations (Admin)</h3>

                    <div class="space-y-4">
                        <!-- Test 5: Create Product -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">5. Create Product (POST /api/v1/products/)</h4>
                            <div class="space-y-2 mb-2">
                                <input type="text" id="create-name" placeholder="Product Name"
                                    class="border rounded px-2 py-1 text-sm w-full" value="Test Product">
                                <textarea id="create-description" placeholder="Description" class="border rounded px-2 py-1 text-sm w-full"
                                    rows="2">Test product description</textarea>
                                <div class="grid grid-cols-2 gap-2">
                                    <input type="number" id="create-price" placeholder="Price"
                                        class="border rounded px-2 py-1 text-sm" value="99.99">
                                    <input type="number" id="create-stock" placeholder="Stock"
                                        class="border rounded px-2 py-1 text-sm" value="10">
                                </div>
                                <input type="text" id="create-brand" placeholder="Brand"
                                    class="border rounded px-2 py-1 text-sm w-full" value="Test Brand">
                            </div>
                            <button onclick="testCreateProduct()"
                                class="bg-green-600 text-white px-4 py-2 rounded text-sm hover:bg-green-700">
                                Test Create Product
                            </button>
                        </div>

                        <!-- Test 6: Update Product -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">6. Update Product (PUT /api/v1/products/{id})
                            </h4>
                            <div class="space-y-2 mb-2">
                                <input type="text" id="update-id" placeholder="Product ID to Update"
                                    class="border rounded px-2 py-1 text-sm w-full">
                                <input type="text" id="update-name" placeholder="New Name"
                                    class="border rounded px-2 py-1 text-sm w-full" value="Updated Test Product">
                                <input type="number" id="update-price" placeholder="New Price"
                                    class="border rounded px-2 py-1 text-sm w-full" value="149.99">
                            </div>
                            <button onclick="testUpdateProduct()"
                                class="bg-yellow-600 text-white px-4 py-2 rounded text-sm hover:bg-yellow-700">
                                Test Update Product
                            </button>
                        </div>

                        <!-- Test 7: Delete Product -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">7. Delete Product (DELETE /api/v1/products/{id})
                            </h4>
                            <div class="flex space-x-2 mb-2">
                                <input type="text" id="delete-id" placeholder="Product ID to Delete"
                                    class="border rounded px-2 py-1 text-sm flex-1">
                            </div>
                            <button onclick="testDeleteProduct()"
                                class="bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-700">
                                Test Delete Product
                            </button>
                        </div>

                        <!-- Test 8: Add to Cart -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">8. Add to Cart</h4>
                            <div class="flex space-x-2 mb-2">
                                <input type="text" id="cart-product-id" placeholder="Product ID"
                                    class="border rounded px-2 py-1 text-sm flex-1">
                                <input type="number" id="cart-quantity" placeholder="Quantity"
                                    class="border rounded px-2 py-1 text-sm" value="1">
                            </div>
                            <button onclick="testAddToCart()"
                                class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                                Test Add to Cart
                            </button>
                        </div>

                        <!-- Test 9: Add to Wishlist -->
                        <div class="border border-gray-200 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-2">9. Add to Wishlist</h4>
                            <div class="flex space-x-2 mb-2">
                                <input type="text" id="wishlist-product-id" placeholder="Product ID"
                                    class="border rounded px-2 py-1 text-sm flex-1">
                            </div>
                            <button onclick="testAddToWishlist()"
                                class="bg-pink-600 text-white px-4 py-2 rounded text-sm hover:bg-pink-700">
                                Test Add to Wishlist
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Run All Tests -->
            <div class="mt-8 text-center">
                <button onclick="runAllTests()"
                    class="bg-gray-800 text-white px-8 py-3 rounded-lg text-lg font-semibold hover:bg-gray-900">
                    🚀 Run All Tests
                </button>
            </div>
        </div>
    </div>

    <script>
        let testResults = [];

        function showResult(testName, success, data, error = null) {
            const resultDiv = document.getElementById('test-results');
            const timestamp = new Date().toLocaleTimeString();

            const resultHtml = `
                <div class="border rounded-lg p-4 ${success ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'}">
                    <div class="flex justify-between items-start mb-2">
                        <h4 class="font-semibold ${success ? 'text-green-800' : 'text-red-800'}">${testName}</h4>
                        <span class="text-sm text-gray-500">${timestamp}</span>
                    </div>
                    <div class="text-sm ${success ? 'text-green-700' : 'text-red-700'}">
                        ${success ? '✅ Success' : '❌ Failed'}
                        ${error ? `: ${error}` : ''}
                    </div>
                    ${data ? `<pre class="mt-2 text-xs bg-white p-2 rounded border overflow-auto max-h-32">${JSON.stringify(data, null, 2)}</pre>` : ''}
                </div>
            `;

            resultDiv.insertAdjacentHTML('afterbegin', resultHtml);
            testResults.push({
                testName,
                success,
                data,
                error,
                timestamp
            });
        }

        async function testListProducts() {
            try {
                const params = new URLSearchParams();
                const limit = document.getElementById('limit').value;
                const offset = document.getElementById('offset').value;
                const sort = document.getElementById('sort').value;

                if (limit) params.append('limit', limit);
                if (offset) params.append('offset', offset);
                if (sort) params.append('sort', sort);

                const response = await fetch(`/api/products?${params.toString()}`);
                const data = await response.json();

                if (response.ok) {
                    showResult('List Products', true, data);
                } else {
                    showResult('List Products', false, data, `HTTP ${response.status}`);
                }
            } catch (error) {
                showResult('List Products', false, null, error.message);
            }
        }

        async function testSearchProducts() {
            try {
                const params = new URLSearchParams();
                const query = document.getElementById('search-query').value;
                const limit = document.getElementById('search-limit').value;

                if (query) params.append('q', query);
                if (limit) params.append('limit', limit);

                const response = await fetch(`/api/products/search?${params.toString()}`);
                const data = await response.json();

                if (response.ok) {
                    showResult('Search Products', true, data);
                } else {
                    showResult('Search Products', false, data, `HTTP ${response.status}`);
                }
            } catch (error) {
                showResult('Search Products', false, null, error.message);
            }
        }

        async function testGetProduct() {
            try {
                const productId = document.getElementById('product-id').value;
                if (!productId) {
                    showResult('Get Product', false, null, 'Product ID is required');
                    return;
                }

                const response = await fetch(`/products/${productId}`);

                if (response.ok) {
                    showResult('Get Product', true, {
                        message: 'Product page loaded successfully'
                    });
                } else {
                    showResult('Get Product', false, null, `HTTP ${response.status}`);
                }
            } catch (error) {
                showResult('Get Product', false, null, error.message);
            }
        }

        async function testGetCategories() {
            try {
                const response = await fetch('/api/categories');
                const data = await response.json();

                if (response.ok) {
                    showResult('Get Categories', true, data);
                } else {
                    showResult('Get Categories', false, data, `HTTP ${response.status}`);
                }
            } catch (error) {
                showResult('Get Categories', false, null, error.message);
            }
        }

        async function getFirstProductId() {
            try {
                const response = await fetch('/api/products?limit=1');
                const data = await response.json();

                // Handle different response formats
                let products = [];
                if (Array.isArray(data)) {
                    products = data;
                } else if (data.products && Array.isArray(data.products)) {
                    products = data.products;
                } else if (data.data && Array.isArray(data.data)) {
                    products = data.data;
                }

                if (response.ok && products.length > 0) {
                    const firstProduct = products[0];
                    document.getElementById('product-id').value = firstProduct.id;
                    document.getElementById('update-id').value = firstProduct.id;
                    document.getElementById('cart-product-id').value = firstProduct.id;
                    document.getElementById('wishlist-product-id').value = firstProduct.id;
                    showResult('Get First Product ID', true, {
                        id: firstProduct.id,
                        name: firstProduct.name
                    });
                } else {
                    showResult('Get First Product ID', false, data, 'No products found');
                }
            } catch (error) {
                showResult('Get First Product ID', false, null, error.message);
            }
        }

        async function testCreateProduct() {
            try {
                const productData = {
                    name: document.getElementById('create-name').value,
                    description: document.getElementById('create-description').value,
                    price: parseFloat(document.getElementById('create-price').value),
                    stock_quantity: parseInt(document.getElementById('create-stock').value),
                    brand: document.getElementById('create-brand').value
                };

                const response = await fetch('/api/admin/products', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: JSON.stringify(productData)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showResult('Create Product', true, data);
                    if (data.product && data.product.id) {
                        document.getElementById('update-id').value = data.product.id;
                        document.getElementById('delete-id').value = data.product.id;
                    }
                } else {
                    showResult('Create Product', false, data, data.message || `HTTP ${response.status}`);
                }
            } catch (error) {
                showResult('Create Product', false, null, error.message);
            }
        }

        async function testUpdateProduct() {
            try {
                const productId = document.getElementById('update-id').value;
                if (!productId) {
                    showResult('Update Product', false, null, 'Product ID is required');
                    return;
                }

                const productData = {
                    name: document.getElementById('update-name').value,
                    price: parseFloat(document.getElementById('update-price').value)
                };

                const response = await fetch(`/api/admin/products/${productId}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: JSON.stringify(productData)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showResult('Update Product', true, data);
                } else {
                    showResult('Update Product', false, data, data.message || `HTTP ${response.status}`);
                }
            } catch (error) {
                showResult('Update Product', false, null, error.message);
            }
        }

        async function testDeleteProduct() {
            try {
                const productId = document.getElementById('delete-id').value;
                if (!productId) {
                    showResult('Delete Product', false, null, 'Product ID is required');
                    return;
                }

                if (!confirm('Are you sure you want to delete this product?')) {
                    return;
                }

                const response = await fetch(`/api/admin/products/${productId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showResult('Delete Product', true, data);
                } else {
                    showResult('Delete Product', false, data, data.message || `HTTP ${response.status}`);
                }
            } catch (error) {
                showResult('Delete Product', false, null, error.message);
            }
        }

        async function testAddToCart() {
            try {
                const productId = document.getElementById('cart-product-id').value;
                const quantity = document.getElementById('cart-quantity').value;

                if (!productId) {
                    showResult('Add to Cart', false, null, 'Product ID is required');
                    return;
                }

                const response = await fetch(`/api/products/${productId}/cart`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    },
                    body: JSON.stringify({
                        quantity: parseInt(quantity)
                    })
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showResult('Add to Cart', true, data);
                } else {
                    showResult('Add to Cart', false, data, data.message || `HTTP ${response.status}`);
                }
            } catch (error) {
                showResult('Add to Cart', false, null, error.message);
            }
        }

        async function testAddToWishlist() {
            try {
                const productId = document.getElementById('wishlist-product-id').value;

                if (!productId) {
                    showResult('Add to Wishlist', false, null, 'Product ID is required');
                    return;
                }

                const response = await fetch(`/api/products/${productId}/wishlist`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    }
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    showResult('Add to Wishlist', true, data);
                } else {
                    showResult('Add to Wishlist', false, data, data.message || `HTTP ${response.status}`);
                }
            } catch (error) {
                showResult('Add to Wishlist', false, null, error.message);
            }
        }

        async function runAllTests() {
            document.getElementById('test-results').innerHTML = '';
            testResults = [];

            showResult('Starting All Tests', true, {
                message: 'Running comprehensive product API tests...'
            });

            // Run tests in sequence with delays
            await new Promise(resolve => setTimeout(resolve, 500));
            await testGetCategories();

            await new Promise(resolve => setTimeout(resolve, 500));
            await testListProducts();

            await new Promise(resolve => setTimeout(resolve, 500));
            await testSearchProducts();

            await new Promise(resolve => setTimeout(resolve, 500));
            await getFirstProductId();

            await new Promise(resolve => setTimeout(resolve, 500));
            await testGetProduct();

            await new Promise(resolve => setTimeout(resolve, 500));
            await testCreateProduct();

            await new Promise(resolve => setTimeout(resolve, 500));
            await testUpdateProduct();

            await new Promise(resolve => setTimeout(resolve, 500));
            await testAddToCart();

            await new Promise(resolve => setTimeout(resolve, 500));
            await testAddToWishlist();

            // Don't auto-delete in comprehensive test
            // await new Promise(resolve => setTimeout(resolve, 500));
            // await testDeleteProduct();

            const successCount = testResults.filter(r => r.success).length;
            const totalCount = testResults.length;

            showResult('All Tests Complete', true, {
                summary: `${successCount}/${totalCount} tests passed`,
                results: testResults
            });
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
<?php /**PATH C:\Users\HACKER JOE\Desktop\MAIN FOLDER\E-commerce FASTAPI AND LARAVEL\frontend-laravel\resources\views/test/products.blade.php ENDPATH**/ ?>