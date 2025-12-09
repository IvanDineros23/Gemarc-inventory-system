@extends('layouts.app')

@section('title', 'Settings | Gemarc LAN Based Inventory System')

@section('content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <details class="group bg-white shadow sm:rounded-lg" open>
                <summary class="cursor-pointer px-6 py-4 flex items-center justify-between text-left">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Profile Information</h3>
                        <p class="text-sm text-gray-500">Update your account name and email address.</p>
                    </div>
                    <div class="ml-4">
                        <svg class="w-5 h-5 text-gray-400 group-open:rotate-180 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </summary>

                <div class="px-6 pb-6">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </details>

            <details class="group bg-white shadow sm:rounded-lg">
                <summary class="cursor-pointer px-6 py-4 flex items-center justify-between text-left">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Update Password</h3>
                        <p class="text-sm text-gray-500">Change your account password.</p>
                    </div>
                    <div class="ml-4">
                        <svg class="w-5 h-5 text-gray-400 group-open:rotate-180 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </summary>

                <div class="px-6 pb-6">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </details>

            <details class="group bg-white shadow sm:rounded-lg">
                <summary class="cursor-pointer px-6 py-4 flex items-center justify-between text-left">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Delete Account</h3>
                        <p class="text-sm text-gray-500">Permanently delete your account.</p>
                    </div>
                    <div class="ml-4">
                        <svg class="w-5 h-5 text-gray-400 group-open:rotate-180 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </summary>

                <div class="px-6 pb-6">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </details>

            <details class="group bg-white shadow sm:rounded-lg">
                <summary class="cursor-pointer px-6 py-4 flex items-center justify-between text-left">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900">Database Backup & Restore</h3>
                        <p class="text-sm text-gray-500">Create a database backup or restore from an uploaded SQL file.</p>
                    </div>
                    <div class="ml-4">
                        <svg class="w-5 h-5 text-gray-400 group-open:rotate-180 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </summary>

                <div class="px-6 pb-6">
                    <div class="max-w-xl space-y-4">
                        <!-- Backup form -->
                        <form id="db-backup-form" method="POST" action="{{ route('db.backup') }}">
                            @csrf
                            <div class="flex items-center gap-3">
                                <button type="submit" id="backup-btn" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700">Create Backup</button>
                                <p class="text-sm text-gray-500">Click to create a backup and download the SQL dump.</p>
                            </div>
                        </form>

                        <!-- Restore form -->
                        <form id="db-restore-form" method="POST" action="{{ route('db.restore') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="flex items-center gap-3">
                                <input id="sql_file" name="sql_file" type="file" accept=".sql,.txt" required class="block" />
                                <button type="submit" id="restore-btn" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-700">Restore from file</button>
                            </div>
                            <p class="text-sm text-gray-500">Choose a SQL dump file (.sql) and click restore. Use with caution.</p>
                        </form>
                    </div>
                </div>
            </details>
        </div>
    </div>

    <script>
        // Backup form handler
        document.getElementById('db-backup-form').addEventListener('submit', function(e) {
            if (!confirm('Create a database backup now?')) {
                e.preventDefault();
                return;
            }
            
            // Show toast notification
            showToast('Creating backup...', 'info');
            
            // Show success after a delay (file will be downloaded)
            setTimeout(() => {
                showToast('Backup created successfully! Check your downloads.', 'success');
            }, 2000);
        });

        // Restore form handler - submit via AJAX so we can show success toast after completion
        document.getElementById('db-restore-form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const fileInput = document.getElementById('sql_file');
            if (!fileInput.files.length) {
                showToast('Please select a SQL file to restore.', 'error');
                return;
            }

            if (!confirm('This will run the uploaded SQL file against the configured database. Continue?')) {
                return;
            }

            showToast('Restoring database... Please wait.', 'info');

            try {
                const form = document.getElementById('db-restore-form');
                const url = form.getAttribute('action');
                const formData = new FormData(form);

                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                const resp = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                // First, fade out all existing toasts (short duration so blue disappears faster)
                const existingToasts = document.querySelectorAll('.toast-notification');
                existingToasts.forEach(t => {
                    t.style.opacity = '0';
                });

                // Wait briefly for fade animation to complete AND remove from DOM
                await new Promise(resolve => setTimeout(resolve, 180));
                existingToasts.forEach(t => t.remove());

                // Small buffer before showing the next toast
                await new Promise(resolve => setTimeout(resolve, 120));

                if (resp.ok) {
                    const data = await resp.json();
                    showToast(data.message || 'Database restored successfully.', 'success');

                    // Show loading message after success toast is visible
                    setTimeout(async () => {
                        // Remove success toast smoothly
                        const successToast = document.querySelector('.toast-notification');
                        if (successToast) {
                            successToast.style.opacity = '0';
                            await new Promise(resolve => setTimeout(resolve, 300));
                            successToast.remove();
                            await new Promise(resolve => setTimeout(resolve, 100));
                        }
                        showToast('Reloading page to show updated data...', 'info');
                    }, 2500);

                    // Reload after user has time to see both messages
                    setTimeout(() => {
                        window.location.reload();
                    }, 4500);
                } else {
                    let message = 'Restore failed. Check logs.';
                    try {
                        const err = await resp.json();
                        message = err.message || message;
                    } catch (ignored) {}
                    showToast(message, 'error');
                }
            } catch (err) {
                // Remove info toast on error
                const infoToasts = document.querySelectorAll('.toast-notification');
                infoToasts.forEach(t => {
                    t.style.opacity = '0';
                });
                
                await new Promise(resolve => setTimeout(resolve, 300));
                infoToasts.forEach(t => t.remove());
                await new Promise(resolve => setTimeout(resolve, 200));
                
                showToast('Restore request failed: ' + err.message, 'error');
            }
        });

        // Toast notification function
        function showToast(message, type = 'success') {
            // Remove existing toasts
            const existingToasts = document.querySelectorAll('.toast-notification');
            existingToasts.forEach(toast => toast.remove());
            
            // Create new toast
            const toast = document.createElement('div');
            toast.className = 'toast-notification fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg text-white z-50 transition-opacity duration-300';
            
            // Set color based on type
            if (type === 'success') {
                toast.classList.add('bg-green-500');
            } else if (type === 'error') {
                toast.classList.add('bg-red-600');
            } else if (type === 'info') {
                toast.classList.add('bg-blue-500');
            }
            
            toast.textContent = message;
            document.body.appendChild(toast);
            
            // Auto-hide after 3 seconds (except for info messages during operations)
            if (type !== 'info') {
                setTimeout(() => {
                    toast.style.opacity = '0';
                    setTimeout(() => toast.remove(), 300);
                }, 3000);
            }
        }

        // Show server-side success/error toasts after redirect
        @if(session('success'))
            document.addEventListener('DOMContentLoaded', function() {
                showToast(@json(session('success')), 'success');
            });
        @endif

        @if(session('error'))
            document.addEventListener('DOMContentLoaded', function() {
                showToast(@json(session('error')), 'error');
            });
        @endif
    </script>
@endsection
