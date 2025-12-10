<x-login-layout title="Login | Gemarc Inventory System">
    <div class="min-h-screen flex flex-row bg-gray-100" style="background-image: url('{{ asset('images/stockroombg.png') }}'); background-size: cover;">
        <!-- Sidebar -->
        <aside class="w-96 bg-white text-black flex flex-col items-center justify-center text-center py-8 px-5 min-h-screen">
            <!-- Logo -->
            <div class="mb-6">
                <img src="{{ asset('images/gemarclogo.png') }}" alt="Gemarc Logo" class="h-20 w-auto object-contain mx-auto">
            </div>

            <!-- Title + Description -->
            <div class="mb-6 px-2">
                <h1 class="text-lg font-semibold leading-tight text-black">LAN Based Inventory System</h1>
                <br>
                <p class="text-sm text-black mt-4">Manage products, stocks, and transactions in one centralized system.</p>
            </div>

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="w-full max-w-md text-left">
                @csrf

                <!-- Username -->
                <div class="mt-4">
                    <x-input-label for="email" :value="__('Username')" class="text-black font-bold" />
                    <x-text-input id="email" class="block mt-1 w-full text-black focus:border-gray-400 focus:ring-gray-300" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" class="text-black font-bold" />

                    <div class="flex items-center w-full bg-white rounded-lg border border-gray-300 focus-within:ring-2 focus-within:ring-gray-300">
                        <!-- Password Input -->
                        <input 
                            id="password"
                            type="password"
                            name="password"
                            required
                            autocomplete="current-password"
                            class="flex-1 px-3 py-2 rounded-lg text-black focus:outline-none"
                        >

                        <!-- Toggle Button -->
                        <button 
                            type="button" 
                            onclick="togglePasswordVisibility()" 
                            class="px-3 flex items-center justify-center text-gray-400 hover:text-gray-600"
                        >
                            <!-- Eye (visible by default) -->
                            <svg id="eye-open" xmlns="http://www.w3.org/2000/svg" 
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                 class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>

                            <!-- Eye Off (hidden initially) -->
                            <svg id="eye-closed" xmlns="http://www.w3.org/2000/svg"
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                 class="h-5 w-5 hidden">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7 
                                         0.51-1.627 1.482-3.07 2.769-4.157m3.22-1.855A9.956 9.956 0 0112 5
                                         c4.478 0 8.268 2.943 9.542 7-.305.974-.764 1.887-1.35 2.7M15 12a3 3 0 11-6 0 
                                         3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 3l18 18" />
                            </svg>
                        </button>
                    </div>

                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="mt-4 flex flex-col items-start">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 accent-green-600 shadow-sm focus:ring-green-500 focus:ring-offset-0" name="remember">
                        <span class="ms-2 text-sm text-black">{{ __('Remember me') }}</span>
                    </label>

                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800 mt-2" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>

                <div class="flex items-center justify-end mt-4">
                    <x-primary-button class="ms-3 bg-green-600 text-white hover:bg-green-700">
                        {{ __('Log in') }}
                    </x-primary-button>

                    <a href="{{ route('register') }}" class="ms-3 bg-orange-600 text-white hover:bg-orange-700 px-4 py-2 rounded-lg font-bold">
                        {{ __('Sign up') }}
                    </a>
                </div>
            </form>

            <!-- Footer -->
            <footer class="text-center text-sm text-gray-500 py-4">
                © 2025 Gemarc Enterprises Incorporated
            </footer>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 min-h-screen flex items-center justify-center">
            <!-- Optional: Add a welcome message or leave blank for now -->
        </main>
    </div>

    <script>
    // Prevent browser cache and back button
    (function() {
        // Check if user is authenticated and redirect immediately
        @auth
            window.location.replace("{{ route('dashboard') }}");
        @endauth

        // Disable browser cache
        window.history.forward();
        
        // Prevent pageshow events (back/forward cache)
        window.addEventListener('pageshow', function(event) {
            if (event.persisted || performance.navigation.type === 2) {
                // Check auth status on page show
                @auth
                    window.location.replace("{{ route('dashboard') }}");
                @else
                    window.location.reload();
                @endauth
            }
        });

        // Prevent popstate (back button)
        window.addEventListener('popstate', function(event) {
            window.history.forward();
        });
    })();

    function togglePasswordVisibility() {
        const input = document.getElementById("password");
        const eyeOpen = document.getElementById("eye-open");
        const eyeClosed = document.getElementById("eye-closed");

        const show = input.type === "password";

        input.type = show ? "text" : "password";
        eyeOpen.classList.toggle("hidden", show);     // hide open eye
        eyeClosed.classList.toggle("hidden", !show);  // show closed eye
    }
    </script>
</x-login-layout>
