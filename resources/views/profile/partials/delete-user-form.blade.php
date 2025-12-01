<div class="mt-2">
    <div class="mb-4 rounded border bg-red-50/60 p-4">
        <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-red-600 mt-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z" />
            </svg>
            <div>
                <p class="text-sm font-semibold text-red-700">Danger Zone</p>
                <p class="mt-1 text-xs text-red-600">Once your account is deleted, all of its resources and data will be permanently deleted. This action cannot be undone. Please be certain before proceeding.</p>
            </div>
        </div>
    </div>

    <form id="delete-account-form" method="POST" action="{{ route('profile.destroy') }}" class="mt-4">
        @csrf
        @method('delete')

        <div class="mb-3">
            <label for="password" class="block text-sm font-medium text-gray-700">{{ __('Password') }}</label>
            <input id="password" name="password" type="password" required class="mt-1 block w-full border rounded px-3 py-2">
            @error('password', 'userDeletion') <p class="text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <div class="flex items-center gap-3 mb-4">
            <input id="confirm-delete-checkbox" type="checkbox" class="h-4 w-4 text-red-600 border-gray-300 rounded" />
            <label for="confirm-delete-checkbox" class="text-sm text-gray-700">I understand this will permanently delete my account.</label>
        </div>

        <div>
            <button id="delete-account-btn" type="button" disabled class="inline-flex items-center px-4 py-2 bg-red-300 border border-transparent rounded-md font-semibold text-white cursor-not-allowed">{{ __('Delete Account') }}</button>
        </div>
    </form>

    <!-- Confirmation Modal -->
    <div id="confirm-modal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="text-center">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100 mb-4">
                    <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12A9 9 0 113 12a9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Confirm Account Deletion</h3>
                <div class="mb-6">
                    <p class="text-sm text-gray-500">This action is irreversible. Are you sure you want to delete your account? All your data will be permanently removed.</p>
                </div>
                <div class="flex justify-center space-x-4">
                    <button id="confirm-delete" class="px-6 py-2 bg-red-500 text-white text-sm font-medium rounded-md hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300 focus:ring-offset-2 transition-colors">
                        Yes, delete my account
                    </button>
                    <button id="confirm-cancel" class="px-6 py-2 bg-gray-500 text-white text-sm font-medium rounded-md hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300 focus:ring-offset-2 transition-colors">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function(){
            const checkbox = document.getElementById('confirm-delete-checkbox');
            const deleteBtn = document.getElementById('delete-account-btn');
            const form = document.getElementById('delete-account-form');
            const modal = document.getElementById('confirm-modal');
            const overlay = document.getElementById('confirm-modal-overlay');
            const confirmCancel = document.getElementById('confirm-cancel');
            const confirmDelete = document.getElementById('confirm-delete');

            if (!checkbox || !deleteBtn) return;

            checkbox.addEventListener('change', function(){
                if (checkbox.checked) {
                    deleteBtn.disabled = false;
                    deleteBtn.classList.remove('bg-red-300','cursor-not-allowed');
                    deleteBtn.classList.add('bg-red-600');
                } else {
                    deleteBtn.disabled = true;
                    deleteBtn.classList.add('bg-red-300','cursor-not-allowed');
                    deleteBtn.classList.remove('bg-red-600');
                }
            });

            deleteBtn.addEventListener('click', function(e){
                e.preventDefault();
                // show modal
                modal.style.display = 'flex';
                document.body.style.overflow = 'hidden';
                setTimeout(() => modal.classList.add('show'), 10);
            });

            function closeModal(){
                modal.classList.remove('show');
                setTimeout(() => {
                    modal.style.display = 'none';
                    document.body.style.overflow = '';
                }, 200);
            }

            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeModal();
                }
            });
            confirmCancel.addEventListener('click', closeModal);

            confirmDelete.addEventListener('click', function(){
                // submit the form
                form.submit();
            });

            document.addEventListener('keydown', function(e){
                if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
            });
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
