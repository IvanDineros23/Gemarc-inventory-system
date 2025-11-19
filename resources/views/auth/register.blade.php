<x-login-layout title="Register | Gemarc Inventory System">
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

            <!-- Register Form -->
            <form method="POST" action="{{ route('register') }}" class="w-full max-w-md text-left">
                @csrf

                <!-- Name -->
                <div class="mt-4">
                    <x-input-label for="name" :value="__('Name')" class="text-black font-bold" />
                    <x-text-input id="name" class="block mt-1 w-full text-black focus:border-gray-400 focus:ring-gray-300" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Email Address -->
                <div class="mt-4">
                    <x-input-label for="email" :value="__('Email')" class="text-black font-bold" />
                    <x-text-input id="email" class="block mt-1 w-full text-black focus:border-gray-400 focus:ring-gray-300" type="email" name="email" :value="old('email')" required autocomplete="username" />
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
                            autocomplete="new-password"
                            class="flex-1 px-3 py-2 rounded-lg text-black focus:outline-none"
                        >

                        <!-- Toggle Button -->
                        <button 
                            type="button" 
                            onclick="togglePasswordVisibility('password', 'eye-open-password', 'eye-closed-password')" 
                            class="px-3 flex items-center justify-center text-gray-400 hover:text-gray-600"
                        >
                            <!-- Eye (visible by default) -->
                            <svg id="eye-open-password" xmlns="http://www.w3.org/2000/svg" 
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                 class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>

                            <!-- Eye Off (hidden initially) -->
                            <svg id="eye-closed-password" xmlns="http://www.w3.org/2000/svg"
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

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-black font-bold" />

                    <div class="flex items-center w-full bg-white rounded-lg border border-gray-300 focus-within:ring-2 focus-within:ring-gray-300">
                        <!-- Confirm Password Input -->
                        <input 
                            id="password_confirmation"
                            type="password"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            class="flex-1 px-3 py-2 rounded-lg text-black focus:outline-none"
                        >

                        <!-- Toggle Button -->
                        <button 
                            type="button" 
                            onclick="togglePasswordVisibility('password_confirmation', 'eye-open-confirm', 'eye-closed-confirm')" 
                            class="px-3 flex items-center justify-center text-gray-400 hover:text-gray-600"
                        >
                            <!-- Eye (visible by default) -->
                            <svg id="eye-open-confirm" xmlns="http://www.w3.org/2000/svg" 
                                 fill="none" viewBox="0 0 24 24" stroke="currentColor"
                                 class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>

                            <!-- Eye Off (hidden initially) -->
                            <svg id="eye-closed-confirm" xmlns="http://www.w3.org/2000/svg"
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

                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <div class="flex items-center justify-between mt-4">
                    <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                        {{ __('Already registered?') }}
                    </a>

                    <x-primary-button class="ms-4 bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded">
                        {{ __('Register') }}
                    </x-primary-button>
                </div>
            </form>
        </aside>
    </div>

    <script>
    function togglePasswordVisibility(inputId, eyeOpenId, eyeClosedId) {
        const input = document.getElementById(inputId);
        const eyeOpen = document.getElementById(eyeOpenId);
        const eyeClosed = document.getElementById(eyeClosedId);

        const show = input.type === "password";

        input.type = show ? "text" : "password";
        eyeOpen.classList.toggle("hidden", show);     // hide open eye
        eyeClosed.classList.toggle("hidden", !show);  // show closed eye
    }
    </script>
</x-login-layout>
