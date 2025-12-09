{{-- resources/views/welcome.blade.php --}}
<x-guest-layout>

    <div class="min-h-screen flex flex-row bg-gray-100">
        <!-- Sidebar -->
        <aside class="w-64 bg-white text-black flex flex-col items-center justify-between text-center py-8 px-5 min-h-screen">
            <!-- Logo -->
            <div class="mb-6">
                <img src="{{ asset('images/gemarclogo.png') }}" alt="Gemarc Logo" class="h-20 w-auto object-contain mx-auto">
            </div>

            <!-- Title + Description (below logo) -->
            <div class="mb-6 px-2">
                <h1 class="text-lg font-semibold leading-tight text-black">LAN Based Inventory System</h1>
                <br>
                <p class="text-sm text-black mt-4">Manage products, stocks, and transactions.</p>
            </div>

            <!-- Buttons -->
            <div class="space-y-3 w-full px-2">
                <a href="{{ route('login') }}" class="block w-full text-center rounded-lg bg-green-600 px-4 py-3 text-sm font-semibold text-white hover:bg-green-700 transition shadow">Log in</a>
                <a href="{{ route('register') }}" class="block w-full text-center rounded-lg bg-orange-500 px-4 py-3 text-sm font-semibold text-white hover:bg-orange-600 transition shadow">Sign up</a>
            </div>

            <!-- Footer -->
            <footer class="text-center text-sm text-gray-500 py-4">
                © 2025 Gemarc Enterprises Incorporated
            </footer>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 min-h-screen flex items-center justify-center relative">
            <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('{{ asset('images/stockroombg.png') }}');"></div>
            <div class="relative z-10 w-full h-full flex items-center justify-center">
                <!-- Optional: Add a welcome message or leave blank for now -->
            </div>
        </main>
    </div>
</x-guest-layout>
