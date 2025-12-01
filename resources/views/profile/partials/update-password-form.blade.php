<div class="mt-2">
    <form id="password-update-form" class="space-y-6">
        @csrf
        @method('put')

        <!-- Success Message -->
        <div id="password-success-message" class="hidden bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            Password updated successfully!
        </div>

        <div>
            <label for="current_password" class="block text-sm font-medium text-gray-700">{{ __('Current Password') }}</label>
            <div class="relative mt-1">
                <input id="current_password" name="current_password" type="password" required class="block w-full border rounded px-3 py-2 pr-10 focus:border-green-500 focus:ring-1 focus:ring-green-500">
                <button type="button" id="toggle_current_password" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500" aria-label="Toggle password visibility">
                    <!-- eye icon -->
                    <svg id="toggle_current_password_icon" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
            <p id="current_password_error" class="text-sm text-red-600 mt-1 hidden"></p>
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">{{ __('New Password') }}</label>
            <div class="relative mt-1">
                <input id="password" name="password" type="password" required class="block w-full border rounded px-3 py-2 pr-10 focus:border-green-500 focus:ring-1 focus:ring-green-500">
                <button type="button" id="toggle_password" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500" aria-label="Toggle password visibility">
                    <svg id="toggle_password_icon" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
            <p id="password_error" class="text-sm text-red-600 mt-1 hidden"></p>
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">{{ __('Confirm Password') }}</label>
            <div class="relative mt-1">
                <input id="password_confirmation" name="password_confirmation" type="password" required class="block w-full border rounded px-3 py-2 pr-10 focus:border-green-500 focus:ring-1 focus:ring-green-500">
                <button type="button" id="toggle_password_confirmation" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500" aria-label="Toggle password visibility">
                    <svg id="toggle_password_confirmation_icon" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </button>
            </div>
            <p id="password_confirmation_error" class="text-sm text-red-600 mt-1 hidden"></p>
        </div>

        <div class="flex items-center gap-4">
            <button type="button" id="password-save-btn" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed">
                <span class="save-text">{{ __('Save') }}</span>
                <span class="loading-text hidden">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Saving...
                </span>
            </button>
        </div>
    </form>
</div>

<!-- Confirmation Modal -->
<div id="password-confirmation-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 mb-4">
                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Confirm Password Update</h3>
            <div class="mb-6">
                <p class="text-sm text-gray-500">Are you sure you want to update your password? This action cannot be undone.</p>
            </div>
            <div class="flex justify-center space-x-4">
                <button id="confirm-password-update" class="px-6 py-2 bg-green-500 text-white text-sm font-medium rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300 focus:ring-offset-2 transition-colors">
                    Confirm
                </button>
                <button id="cancel-password-update" class="px-6 py-2 bg-gray-500 text-white text-sm font-medium rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition-colors">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<style>
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(3px);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

.modal-content {
    background: white;
    border-radius: 0.75rem;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    padding: 2rem;
    width: 100%;
    max-width: 400px;
    transform: scale(0.95);
    transition: all 0.2s ease-out;
}

.modal-overlay.show .modal-content {
    transform: scale(1);
}

.modal-overlay.show {
    animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('password-update-form');
    const saveBtn = document.getElementById('password-save-btn');
    const modal = document.getElementById('password-confirmation-modal');
    const confirmBtn = document.getElementById('confirm-password-update');
    const cancelBtn = document.getElementById('cancel-password-update');
    const successMessage = document.getElementById('password-success-message');
    
    // Clear previous errors
    function clearErrors() {
        const errorElements = form.querySelectorAll('.text-red-600');
        errorElements.forEach(element => {
            element.classList.add('hidden');
            element.textContent = '';
        });
        
        // Remove error border styling
        const inputs = form.querySelectorAll('input');
        inputs.forEach(input => {
            input.classList.remove('border-red-500');
            input.classList.add('border-gray-300');
        });
        
        successMessage.classList.add('hidden');
    }
    
    // Show errors
    function showErrors(errors) {
        Object.keys(errors).forEach(field => {
            const errorElement = document.getElementById(field + '_error');
            const inputElement = document.getElementById(field);
            
            if (errorElement && inputElement) {
                errorElement.textContent = errors[field][0];
                errorElement.classList.remove('hidden');
                inputElement.classList.add('border-red-500');
                inputElement.classList.remove('border-gray-300');
            }
        });
    }
    
    // Show loading state
    function setLoading(loading) {
        const saveText = saveBtn.querySelector('.save-text');
        const loadingText = saveBtn.querySelector('.loading-text');
        
        if (loading) {
            saveBtn.disabled = true;
            saveText.classList.add('hidden');
            loadingText.classList.remove('hidden');
        } else {
            saveBtn.disabled = false;
            saveText.classList.remove('hidden');
            loadingText.classList.add('hidden');
        }
    }
    
    // Show modal
    saveBtn.addEventListener('click', function() {
        clearErrors();
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden'; // Prevent background scrolling
        setTimeout(() => modal.classList.add('show'), 10); // Add animation class
    });
    
    // Hide modal
    function hideModal() {
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = ''; // Restore background scrolling
        }, 200);
    }
    
    cancelBtn.addEventListener('click', hideModal);
    
    // Click outside modal to close
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            hideModal();
        }
    });
    
    // Escape key to close modal
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.style.display === 'flex') {
            hideModal();
        }
    });
    
    // Confirm password update
    confirmBtn.addEventListener('click', function() {
        hideModal();
        setLoading(true);
        
        const formData = new FormData(form);
        
        fetch('{{ route('password.update') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(errorData => {
                    throw { status: response.status, data: errorData };
                });
            }
            return response.json();
        })
        .then(data => {
            setLoading(false);
            
            if (data.success) {
                // Show success message
                successMessage.classList.remove('hidden');
                
                // Clear form
                form.reset();
                
                // Scroll to top of form to show success message
                successMessage.scrollIntoView({ behavior: 'smooth' });
                
                // Auto-hide success message after 5 seconds
                setTimeout(() => {
                    successMessage.classList.add('hidden');
                }, 5000);
            }
        })
        .catch(error => {
            setLoading(false);
            
            if (error.status === 422 && error.data && error.data.errors) {
                // Validation errors
                showErrors(error.data.errors);
            } else {
                // Network or other errors
                alert('An error occurred. Please try again.');
                console.error('Error:', error);
            }
        });
    });

    // Password visibility toggles
    function setupToggle(inputId, btnId, iconId) {
        const input = document.getElementById(inputId);
        const btn = document.getElementById(btnId);
        const icon = document.getElementById(iconId);
        if (!input || !btn || !icon) return;

        const eyeOpen = icon.innerHTML; // initial open-eye markup (from template)
        const eyeClosed = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 012.223-3.371M6.6 6.6A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7-.379 1.207-.97 2.338-1.735 3.322M3 3l18 18" />';

        btn.addEventListener('click', function() {
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = eyeClosed;
            } else {
                input.type = 'password';
                icon.innerHTML = eyeOpen;
            }
        });
    }

    // initialize toggles for password fields
    setupToggle('current_password', 'toggle_current_password', 'toggle_current_password_icon');
    setupToggle('password', 'toggle_password', 'toggle_password_icon');
    setupToggle('password_confirmation', 'toggle_password_confirmation', 'toggle_password_confirmation_icon');
});
</script>
<!-- end password partial -->
