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
                                <button type="submit" onclick="return confirm('Create a database backup now?')" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-white hover:bg-blue-700">Create Backup</button>
                                <p class="text-sm text-gray-500">Click to create a backup and download the SQL dump.</p>
                            </div>
                        </form>

                        <!-- Restore form -->
                        <form id="db-restore-form" method="POST" action="{{ route('db.restore') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="flex items-center gap-3">
                                <input id="sql_file" name="sql_file" type="file" accept=".sql,.txt" required class="block" />
                                <button type="submit" onclick="return confirm('This will run the uploaded SQL file against the configured database. Continue?')" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-white hover:bg-red-700">Restore from file</button>
                            </div>
                            <p class="text-sm text-gray-500">Choose a SQL dump file (.sql) and click restore. Use with caution.</p>
                        </form>
                    </div>
                </div>
            </details>
        </div>
    </div>
@endsection
