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
            <?php echo e(__('Profile')); ?>

        </h2>
     <?php $__env->endSlot(); ?>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Profile Information -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Profile Information</h3>
                    <p class="text-sm text-gray-600">Update your account's profile information and email address.</p>
                </div>
                <div class="p-6">
                    <form id="profile-form" class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="full_name" class="block text-sm font-medium text-gray-700 mb-2">Full
                                    Name</label>
                                <input type="text" id="full_name" name="full_name" value="<?php echo e($user['full_name']); ?>"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                <input type="email" id="email" name="email" value="<?php echo e($user['email']); ?>"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                                <input type="tel" id="phone" name="phone" value="<?php echo e($user['phone'] ?? ''); ?>"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">Date of
                                    Birth</label>
                                <input type="date" id="date_of_birth" name="date_of_birth"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" id="save-btn"
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                <span id="save-text">Save Changes</span>
                                <span id="save-loading" class="hidden">
                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Saving...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Addresses -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Addresses</h3>
                            <p class="text-sm text-gray-600">Manage your delivery addresses.</p>
                        </div>
                        <button
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition-colors text-sm">
                            Add Address
                        </button>
                    </div>
                </div>
                <div class="p-6" id="addresses-list">
                    <div class="text-center py-8 text-gray-500">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <span class="text-3xl">📍</span>
                        </div>
                        <p class="text-lg font-medium text-gray-900 mb-2">No addresses saved</p>
                        <p class="text-sm text-gray-600">Add your delivery addresses for faster checkout</p>
                    </div>
                </div>
            </div>

            <!-- Account Security -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100">
                <div class="p-6 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Account Security</h3>
                    <p class="text-sm text-gray-600">Update your password and security settings.</p>
                </div>
                <div class="p-6">
                    <form class="space-y-6">
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current
                                Password</label>
                            <input type="password" id="current_password" name="current_password"
                                class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">New
                                    Password</label>
                                <input type="password" id="new_password" name="new_password"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                            <div>
                                <label for="confirm_password"
                                    class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                                <input type="password" id="confirm_password" name="confirm_password"
                                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit"
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                                Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            loadUserProfile();
            setupProfileForm();
            setupPasswordForm();
        });

        async function loadUserProfile() {
            try {
                // GET /auth/profile from FastAPI
                const response = await fetch('/api/user/profile', {
                    headers: {
                        'Authorization': 'Bearer ' + '<?php echo e(Session::get('access_token')); ?>',
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    const profile = await response.json();
                    populateProfileForm(profile);
                } else {
                    console.error('Failed to load profile');
                }
            } catch (error) {
                console.error('Error loading profile:', error);
            }
        }

        function populateProfileForm(profile) {
            document.getElementById('full_name').value = profile.full_name || '';
            document.getElementById('email').value = profile.email || '';
            document.getElementById('phone').value = profile.phone || '';
            document.getElementById('date_of_birth').value = profile.date_of_birth || '';
        }

        function setupProfileForm() {
            const form = document.querySelector('#profile-form') || document.querySelector('form');
            if (form) {
                form.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    await updateProfile();
                });
            }
        }

        async function updateProfile() {
            const saveBtn = document.getElementById('save-btn') || document.querySelector('button[type="submit"]');
            const saveText = document.getElementById('save-text') || saveBtn;
            const saveLoading = document.getElementById('save-loading');

            try {
                // Show loading state
                if (saveLoading) {
                    saveText.classList.add('hidden');
                    saveLoading.classList.remove('hidden');
                } else {
                    saveBtn.disabled = true;
                    saveBtn.textContent = 'Saving...';
                }

                const formData = {
                    full_name: document.getElementById('full_name').value,
                    email: document.getElementById('email').value,
                    phone: document.getElementById('phone').value,
                    date_of_birth: document.getElementById('date_of_birth').value
                };

                // PUT /auth/profile to FastAPI
                const response = await fetch('/api/user/profile/update', {
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + '<?php echo e(Session::get('access_token')); ?>',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                if (response.ok) {
                    showSuccessMessage('Profile updated successfully!');
                    // Update session data if needed
                    const updatedProfile = await response.json();
                    if (updatedProfile.user) {
                        // Update the session user data
                        await updateSessionUser(updatedProfile.user);
                    }
                } else {
                    const errorData = await response.json();
                    showErrorMessage(errorData.message || 'Failed to update profile');
                }
            } catch (error) {
                console.error('Error updating profile:', error);
                showErrorMessage('An error occurred while updating your profile');
            } finally {
                // Reset button state
                if (saveLoading) {
                    saveText.classList.remove('hidden');
                    saveLoading.classList.add('hidden');
                } else {
                    saveBtn.disabled = false;
                    saveBtn.textContent = 'Save Changes';
                }
            }
        }

        function setupPasswordForm() {
            const passwordForm = document.querySelector('form:last-of-type');
            if (passwordForm) {
                passwordForm.addEventListener('submit', async function(e) {
                    e.preventDefault();
                    await updatePassword();
                });
            }
        }

        async function updatePassword() {
            const currentPassword = document.getElementById('current_password').value;
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (newPassword !== confirmPassword) {
                showErrorMessage('New passwords do not match');
                return;
            }

            try {
                const response = await fetch('/api/user/password/update', {
                    method: 'PUT',
                    headers: {
                        'Authorization': 'Bearer ' + '<?php echo e(Session::get('access_token')); ?>',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        current_password: currentPassword,
                        new_password: newPassword
                    })
                });

                if (response.ok) {
                    showSuccessMessage('Password updated successfully!');
                    // Clear password fields
                    document.getElementById('current_password').value = '';
                    document.getElementById('new_password').value = '';
                    document.getElementById('confirm_password').value = '';
                } else {
                    const errorData = await response.json();
                    showErrorMessage(errorData.message || 'Failed to update password');
                }
            } catch (error) {
                console.error('Error updating password:', error);
                showErrorMessage('An error occurred while updating your password');
            }
        }

        function showSuccessMessage(message) {
            showMessage(message, 'success');
        }

        function showErrorMessage(message) {
            showMessage(message, 'error');
        }

        function showMessage(message, type) {
            // Remove existing messages
            const existingMessages = document.querySelectorAll('.alert-message');
            existingMessages.forEach(msg => msg.remove());

            const alertClass = type === 'success' ? 'bg-green-100 border-green-400 text-green-700' :
                'bg-red-100 border-red-400 text-red-700';

            const messageDiv = document.createElement('div');
            messageDiv.className = `alert-message border px-4 py-3 rounded mb-4 ${alertClass}`;
            messageDiv.innerHTML = `
                <span class="block sm:inline">${message}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentElement.remove()">
                    <svg class="fill-current h-6 w-6" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <title>Close</title>
                        <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
                    </svg>
                </span>
            `;

            // Insert at the top of the main content
            const mainContent = document.querySelector('.max-w-4xl');
            if (mainContent) {
                mainContent.insertBefore(messageDiv, mainContent.firstChild);
            }
        }

        async function updateSessionUser(userData) {
            try {
                await fetch('/api/user/session/update', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + '<?php echo e(Session::get('access_token')); ?>',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(userData)
                });
            } catch (error) {
                console.error('Error updating session:', error);
            }
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
<?php /**PATH C:\Users\HACKER JOE\Desktop\MAIN FOLDER\E-commerce FASTAPI AND LARAVEL\frontend-laravel\resources\views/profile/edit.blade.php ENDPATH**/ ?>