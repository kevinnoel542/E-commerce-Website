<!DOCTYPE html>
<html>
<head>
    <title>Image Test</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Image Display Test</h1>
        
        <!-- Test 1: Direct URL -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h2 class="text-lg font-semibold mb-4">Test 1: Direct Image URL</h2>
            <div class="w-64 h-64 bg-gray-200 rounded-lg overflow-hidden">
                <img src="http://localhost:3000/storage/uploads/products/687373e71de70.jpeg" 
                     alt="Test Image" 
                     class="w-full h-full object-cover"
                     onload="console.log('Image loaded successfully')"
                     onerror="console.log('Image failed to load'); this.parentElement.innerHTML='<div class=\'flex items-center justify-center h-full text-gray-500\'>❌ Failed</div>'">
            </div>
        </div>

        <!-- Test 2: Asset Helper -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h2 class="text-lg font-semibold mb-4">Test 2: Laravel Asset Helper</h2>
            <div class="w-64 h-64 bg-gray-200 rounded-lg overflow-hidden">
                <img src="<?php echo e(asset('storage/uploads/products/687373e71de70.jpeg')); ?>" 
                     alt="Test Image" 
                     class="w-full h-full object-cover"
                     onload="console.log('Asset helper image loaded successfully')"
                     onerror="console.log('Asset helper image failed to load'); this.parentElement.innerHTML='<div class=\'flex items-center justify-center h-full text-gray-500\'>❌ Failed</div>'">
            </div>
        </div>

        <!-- Test 3: Simulated Product Data -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h2 class="text-lg font-semibold mb-4">Test 3: Simulated Product Logic</h2>
            <?php
                $testProduct = [
                    'images' => ['http://localhost:3000/storage/uploads/products/687373e71de70.jpeg'],
                    'name' => 'Test Product'
                ];
                
                $hasImages = isset($testProduct['images']) && is_array($testProduct['images']) && count($testProduct['images']) > 0;
                $imageUrl = '';
                if ($hasImages) {
                    $imageUrl = $testProduct['images'][0];
                    if (strpos($imageUrl, 'http') !== 0) {
                        $imageUrl = asset('storage' . $imageUrl);
                    }
                }
            ?>
            
            <div class="mb-4">
                <strong>Debug Info:</strong><br>
                Has Images: <?php echo e($hasImages ? 'Yes' : 'No'); ?><br>
                Image URL: <?php echo e($imageUrl); ?><br>
                Image Count: <?php echo e(count($testProduct['images'])); ?>

            </div>
            
            <div class="w-64 h-64 bg-gray-200 rounded-lg overflow-hidden">
                <?php if($hasImages && $imageUrl): ?>
                    <img src="<?php echo e($imageUrl); ?>" 
                         alt="<?php echo e($testProduct['name']); ?>" 
                         class="w-full h-full object-cover"
                         onload="console.log('Simulated product image loaded successfully')"
                         onerror="console.log('Simulated product image failed to load'); this.parentElement.innerHTML='<div class=\'flex items-center justify-center h-full text-gray-500\'>❌ Failed</div>'">
                <?php else: ?>
                    <div class="flex items-center justify-center h-full text-gray-500 text-4xl">📦</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Test 4: Old Format URL -->
        <div class="bg-white p-6 rounded-lg shadow mb-6">
            <h2 class="text-lg font-semibold mb-4">Test 4: Old Format URL Conversion</h2>
            <?php
                $oldFormatProduct = [
                    'images' => ['/uploads/products/9fe8b45e-f369-45c8-abab-99aac9a5971a.jpg'],
                    'name' => 'Old Format Product'
                ];
                
                $hasOldImages = isset($oldFormatProduct['images']) && is_array($oldFormatProduct['images']) && count($oldFormatProduct['images']) > 0;
                $oldImageUrl = '';
                if ($hasOldImages) {
                    $oldImageUrl = $oldFormatProduct['images'][0];
                    if (strpos($oldImageUrl, 'http') !== 0) {
                        $oldImageUrl = asset('storage' . $oldImageUrl);
                    }
                }
            ?>
            
            <div class="mb-4">
                <strong>Debug Info:</strong><br>
                Original URL: <?php echo e($oldFormatProduct['images'][0]); ?><br>
                Converted URL: <?php echo e($oldImageUrl); ?><br>
                Has Images: <?php echo e($hasOldImages ? 'Yes' : 'No'); ?>

            </div>
            
            <div class="w-64 h-64 bg-gray-200 rounded-lg overflow-hidden">
                <?php if($hasOldImages && $oldImageUrl): ?>
                    <img src="<?php echo e($oldImageUrl); ?>" 
                         alt="<?php echo e($oldFormatProduct['name']); ?>" 
                         class="w-full h-full object-cover"
                         onload="console.log('Old format image loaded successfully')"
                         onerror="console.log('Old format image failed to load'); this.parentElement.innerHTML='<div class=\'flex items-center justify-center h-full text-gray-500\'>❌ Failed</div>'">
                <?php else: ?>
                    <div class="flex items-center justify-center h-full text-gray-500 text-4xl">📦</div>
                <?php endif; ?>
            </div>
        </div>

        <!-- JavaScript Console Output -->
        <div class="bg-gray-800 text-green-400 p-4 rounded-lg">
            <h3 class="text-lg font-semibold mb-2">Console Output:</h3>
            <div id="console-output" class="font-mono text-sm">
                Check browser console for image loading results...
            </div>
        </div>
    </div>

    <script>
        // Override console.log to display in page
        const originalLog = console.log;
        const outputDiv = document.getElementById('console-output');
        
        console.log = function(...args) {
            originalLog.apply(console, args);
            const message = args.join(' ');
            outputDiv.innerHTML += message + '<br>';
        };
    </script>
</body>
</html>
<?php /**PATH C:\Users\HACKER JOE\Desktop\MAIN FOLDER\E-commerce FASTAPI AND LARAVEL\frontend-laravel\resources\views/test/image-test.blade.php ENDPATH**/ ?>