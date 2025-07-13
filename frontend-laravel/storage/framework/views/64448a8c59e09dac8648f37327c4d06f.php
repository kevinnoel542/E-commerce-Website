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
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                <?php echo e(__('Edit Product')); ?>

            </h2>
            <a href="<?php echo e(route('admin.products.index')); ?>"
                class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                <i class="bi bi-arrow-left mr-2"></i>Back to Products
            </a>
        </div>
     <?php $__env->endSlot(); ?>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Error Messages -->
            <?php if(session('error')): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            <!-- Edit Product Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Edit Product Information</h3>
                    <p class="text-sm text-gray-600">Update the product details</p>
                </div>

                <form method="POST" action="<?php echo e(route('admin.products.update', $product['id'])); ?>"
                    enctype="multipart/form-data" class="p-6 space-y-6">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Product Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Product Name
                                *</label>
                            <input type="text" id="name" name="name"
                                value="<?php echo e(old('name', $product['name'] ?? '')); ?>" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Price -->
                        <div>
                            <label for="price" class="block text-sm font-medium text-gray-700 mb-2">Price *</label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-gray-500">$</span>
                                <input type="number" id="price" name="price"
                                    value="<?php echo e(old('price', $product['price'] ?? '')); ?>" step="0.01" min="0"
                                    required
                                    class="w-full border border-gray-300 rounded-lg pl-8 pr-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Category -->
                        <div>
                            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-2">Category
                                *</label>
                            <select id="category_id" name="category_id" required
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select a category</option>
                                <?php if(isset($categories) && count($categories) > 0): ?>
                                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($category['id']); ?>"
                                            <?php echo e(old('category_id', $product['category_id'] ?? '') == $category['id'] ? 'selected' : ''); ?>>
                                            <?php echo e($category['name']); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <?php endif; ?>
                            </select>
                            <?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <!-- Stock Quantity -->
                        <div>
                            <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-2">Stock
                                Quantity</label>
                            <input type="number" id="stock_quantity" name="stock_quantity"
                                value="<?php echo e(old('stock_quantity', $product['stock_quantity'] ?? 0)); ?>" min="0"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <?php $__errorArgs = ['stock_quantity'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description"
                            class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea id="description" name="description" rows="4"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"><?php echo e(old('description', $product['description'] ?? '')); ?></textarea>
                        <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Current Images -->
                    <?php if(isset($product['images']) && is_array($product['images']) && count($product['images']) > 0): ?>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Current Images</label>
                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-4">
                                <?php $__currentLoopData = $product['images']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="relative group">
                                        <div
                                            class="aspect-square bg-gray-100 rounded-lg overflow-hidden border-2 border-gray-200">
                                            <img src="<?php echo e($image); ?>" alt="Product Image <?php echo e($index + 1); ?>"
                                                class="w-full h-full object-cover">
                                        </div>
                                        <div class="absolute top-2 right-2">
                                            <button type="button" onclick="removeCurrentImage('<?php echo e($image); ?>')"
                                                class="bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 transition-colors">
                                                ×
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Add New Images -->
                    <div>
                        <label for="images" class="block text-sm font-medium text-gray-700 mb-2">Add New
                            Images</label>
                        <div
                            class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                            <div class="space-y-2">
                                <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none"
                                    viewBox="0 0 48 48">
                                    <path
                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="text-gray-600">
                                    <label for="images" class="cursor-pointer">
                                        <span class="mt-2 block text-sm font-medium text-blue-600 hover:text-blue-500">
                                            Click to upload new images
                                        </span>
                                        <input id="images" name="images[]" type="file" class="sr-only" multiple
                                            accept="image/*" onchange="previewImages(this)">
                                    </label>
                                    <p class="mt-1 text-xs text-gray-500">PNG, JPG, GIF up to 10MB each (Max 5 images)
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- New Image Preview Container -->
                        <div id="image-preview" class="mt-4 hidden" style="display: none;"
                            data-grid-classes="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">
                            <!-- Previews will be inserted here -->
                        </div>

                        <?php $__errorArgs = ['images'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        <?php $__errorArgs = ['images.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <p class="text-red-500 text-xs mt-1"><?php echo e($message); ?></p>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="<?php echo e(route('admin.products.index')); ?>"
                            class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                            Cancel
                        </a>
                        <button type="submit"
                            class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition">
                            <i class="bi bi-check-circle mr-2"></i>Update Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Same JavaScript functions as in create form
        function previewImages(input) {
            const previewContainer = document.getElementById('image-preview');
            const gridClasses = previewContainer.getAttribute('data-grid-classes');

            previewContainer.innerHTML = '';

            if (input.files && input.files.length > 0) {
                previewContainer.className = `mt-4 ${gridClasses}`;
                previewContainer.style.display = 'grid';

                const filesToShow = Math.min(input.files.length, 5);

                for (let i = 0; i < filesToShow; i++) {
                    const file = input.files[i];

                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            const previewDiv = document.createElement('div');
                            previewDiv.className = 'relative group';

                            previewDiv.innerHTML = `
                                <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden border-2 border-gray-200">
                                    <img src="${e.target.result}" alt="Preview ${i + 1}"
                                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200">
                                </div>
                                <div class="absolute top-2 right-2">
                                    <button type="button" onclick="removeImage(${i})"
                                            class="bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600 transition-colors">
                                        ×
                                    </button>
                                </div>
                                <div class="mt-1 text-xs text-gray-500 text-center truncate">
                                    ${file.name}
                                </div>
                            `;

                            previewContainer.appendChild(previewDiv);
                        };

                        reader.readAsDataURL(file);
                    }
                }

                if (input.files.length > 5) {
                    const warningDiv = document.createElement('div');
                    warningDiv.className = 'col-span-full text-center text-amber-600 text-sm mt-2';
                    warningDiv.innerHTML = `
                        <i class="bi bi-exclamation-triangle mr-1"></i>
                        Only the first 5 images will be uploaded. Please select fewer images.
                    `;
                    previewContainer.appendChild(warningDiv);
                }
            } else {
                previewContainer.className = 'mt-4 hidden';
                previewContainer.style.display = 'none';
            }
        }

        function removeImage(index) {
            const input = document.getElementById('images');
            const dt = new DataTransfer();

            for (let i = 0; i < input.files.length; i++) {
                if (i !== index) {
                    dt.items.add(input.files[i]);
                }
            }

            input.files = dt.files;
            previewImages(input);
        }

        function removeCurrentImage(imageUrl) {
            if (confirm('Are you sure you want to remove this image?')) {
                // Add a hidden input to track removed images
                const form = document.querySelector('form');
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'removed_images[]';
                hiddenInput.value = imageUrl;
                form.appendChild(hiddenInput);

                // Hide the image visually
                event.target.closest('.relative').style.display = 'none';
            }
        }

        // Add drag and drop functionality
        document.addEventListener('DOMContentLoaded', function() {
            const dropZone = document.querySelector('.border-dashed');
            const fileInput = document.getElementById('images');

            if (dropZone && fileInput) {
                ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, preventDefaults, false);
                });

                function preventDefaults(e) {
                    e.preventDefault();
                    e.stopPropagation();
                }

                ['dragenter', 'dragover'].forEach(eventName => {
                    dropZone.addEventListener(eventName, highlight, false);
                });

                ['dragleave', 'drop'].forEach(eventName => {
                    dropZone.addEventListener(eventName, unhighlight, false);
                });

                function highlight(e) {
                    dropZone.classList.add('border-blue-400', 'bg-blue-50');
                }

                function unhighlight(e) {
                    dropZone.classList.remove('border-blue-400', 'bg-blue-50');
                }

                dropZone.addEventListener('drop', handleDrop, false);

                function handleDrop(e) {
                    const dt = e.dataTransfer;
                    const files = dt.files;

                    fileInput.files = files;
                    previewImages(fileInput);
                }
            }
        });
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
<?php /**PATH C:\Users\HACKER JOE\Desktop\MAIN FOLDER\E-commerce FASTAPI AND LARAVEL\frontend-laravel\resources\views/admin/products/edit.blade.php ENDPATH**/ ?>