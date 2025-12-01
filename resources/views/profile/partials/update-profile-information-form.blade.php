<div>
    <form id="profile-info-form" method="POST" action="{{ route('profile.update') }}" class="mt-2 space-y-6">
        @csrf
        @method('patch')

        <div>
            <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Name') }}</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus class="mt-1 block w-full border rounded px-3 py-2">
            @error('name') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required class="mt-1 block w-full border rounded px-3 py-2">
            @error('email') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-4">
            <button id="open-profile-confirm" type="button" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-white hover:bg-green-700">{{ __('Save') }}</button>
        </div>
    </form>

    <!-- Confirmation Modal -->
    <div id="profile-confirm-modal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 mb-4">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Confirm Profile Update</h3>
                <div class="mb-4">
                    <p class="text-sm text-gray-500 mb-4">Enter your current password to confirm updating your profile information.</p>
                    <div class="text-left">
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Current Password') }}</label>
                        <div class="relative">
                            <input id="confirm_password" type="password" class="block w-full border rounded px-3 py-2 pr-10 focus:border-green-500 focus:ring-1 focus:ring-green-500" autocomplete="current-password">
                            <button type="button" id="toggle-confirm-password" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700" aria-label="Show password">
                                <svg id="toggle-confirm-password-icon" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                            </button>
                        </div>
                        <p id="profile-confirm-error" class="text-sm text-red-600 mt-2 hidden"></p>
                    </div>
                </div>
                <div class="flex justify-center space-x-4">
                    <button id="profile-confirm-submit" class="px-6 py-2 bg-green-500 text-white text-sm font-medium rounded-md hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300 focus:ring-offset-2 transition-colors">
                        Confirm
                    </button>
                    <button id="profile-confirm-cancel" class="px-6 py-2 bg-gray-500 text-white text-sm font-medium rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function(){
            const openBtn = document.getElementById('open-profile-confirm');
            const modal = document.getElementById('profile-confirm-modal');
            const overlay = document.getElementById('profile-confirm-overlay');
            const cancel = document.getElementById('profile-confirm-cancel');
            const submit = document.getElementById('profile-confirm-submit');
            const passwordInput = document.getElementById('confirm_password');
            const toggleBtn = document.getElementById('toggle-confirm-password');
            const toggleIcon = document.getElementById('toggle-confirm-password-icon');
            const form = document.getElementById('profile-info-form');
            const errorEl = document.getElementById('profile-confirm-error');

            function openModal(){
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                setTimeout(() => modal.classList.add('show'), 10);
                passwordInput.value = '';
                errorEl.classList.add('hidden');
            }

            function closeModal(){
                modal.classList.remove('show');
                setTimeout(() => {
                    modal.style.display = 'none';
                    document.body.style.overflow = '';
                }, 200);
            }

            openBtn.addEventListener('click', function(){ openModal(); });
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModal();
                }
            });
            cancel.addEventListener('click', closeModal);

            submit.addEventListener('click', function(e){
                e.preventDefault();
                const pw = passwordInput.value.trim();
                if (!pw) {
                    errorEl.textContent = 'Please enter your current password.';
                    errorEl.classList.remove('hidden');
                    return;
                }

                // append hidden input to the form and submit
                let hidden = document.getElementById('current_password_input');
                if (!hidden) {
                    hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = 'current_password';
                    hidden.id = 'current_password_input';
                    form.appendChild(hidden);
                }
                hidden.value = pw;
                form.submit();
            });

            // toggle password visibility
            if (toggleBtn) {
                toggleBtn.addEventListener('click', function(){
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        // eye-off icon
                            toggleIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a9.97 9.97 0 012.223-3.371M6.6 6.6A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.542 7-.379 1.207-.97 2.338-1.735 3.322M3 3l18 18" />';
                    } else {
                        passwordInput.type = 'password';
                        // eye icon
                            toggleIcon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />';
                    }
                });
            }

            document.addEventListener('keydown', function(e){ if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal(); });
        })();
    </script>

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
</div>
