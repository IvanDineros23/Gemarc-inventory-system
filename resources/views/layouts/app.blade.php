<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>@yield('title', config('app.name', 'Laravel'))</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Default favicon (uses Gemarc logo) -->
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/gemarclogo.png') }}" />
        <link rel="shortcut icon" href="{{ asset('images/gemarclogo.png') }}" />

        <!-- Per-page head overrides (e.g., custom favicons) -->
        @yield('head')
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100 dark:bg-gray-900">
            <div class="min-h-screen flex">
                <!-- Sidebar (left) -->
                <aside class="w-64 bg-white text-black flex flex-col items-center justify-between text-center py-8 px-5">
                    <div class="w-full">
                        <div class="mb-6">
                            <a href="{{ route('dashboard') }}">
                                <img src="{{ asset('images/gemarclogo.png') }}" alt="Gemarc Logo" class="h-16 w-auto object-contain mx-auto">
                            </a>
                        </div>

                        <div class="mb-6 px-2">
                            <h1 class="text-lg font-semibold leading-tight text-black">LAN Based Inventory System</h1>
                        </div>

                        <nav class="space-y-2 px-2 mt-4">
                            <a href="{{ route('dashboard') }}" class="block text-start rounded-lg px-3 py-2 hover:bg-gray-100 text-black {{ request()->routeIs('dashboard') ? 'bg-gray-200 font-semibold border-l-4 border-green-600' : '' }}">Dashboard</a>
                            <a href="{{ route('receiving.entry') }}" class="block text-start rounded-lg px-3 py-2 hover:bg-gray-100 text-black {{ request()->routeIs('receiving.entry') ? 'bg-gray-200 font-semibold border-l-4 border-green-600' : '' }}">Receiving Entry</a>
                            <a href="{{ route('inventory.per.supplier') }}" class="block text-start rounded-lg px-3 py-2 hover:bg-gray-100 text-black {{ request()->routeIs('inventory.per.supplier') ? 'bg-gray-200 font-semibold border-l-4 border-green-600' : '' }}">Inventory per Supplier</a>
                            <a href="{{ route('inventory.report') }}" class="block text-start rounded-lg px-3 py-2 hover:bg-gray-100 text-black {{ request()->routeIs('inventory.report') ? 'bg-gray-200 font-semibold border-l-4 border-green-600' : '' }}">Inventory Report</a>
                            <a href="{{ route('consignment.items') }}" class="block text-start rounded-lg px-3 py-2 hover:bg-gray-100 text-black {{ request()->routeIs('consignment.items') ? 'bg-gray-200 font-semibold border-l-4 border-green-600' : '' }}">Consignment Items</a>
                            <a href="{{ route('reorder.level.entry') }}" class="block text-start rounded-lg px-3 py-2 hover:bg-gray-100 text-black {{ request()->routeIs('reorder.level.entry') ? 'bg-gray-200 font-semibold border-l-4 border-green-600' : '' }}">Re-order Level Entry</a>
                            <a href="{{ route('delivery.entry') }}" class="block text-start rounded-lg px-3 py-2 hover:bg-gray-100 text-black {{ request()->routeIs('delivery.entry') ? 'bg-gray-200 font-semibold border-l-4 border-green-600' : '' }}">Delivery Entry</a>
                            <a href="{{ route('stock.movement') }}" class="block text-start rounded-lg px-3 py-2 hover:bg-gray-100 text-black {{ request()->routeIs('stock.movement') ? 'bg-gray-200 font-semibold border-l-4 border-green-600' : '' }}">Stock Movement</a>
                            <a href="{{ route('delivery.review') }}" class="block text-start rounded-lg px-3 py-2 hover:bg-gray-100 text-black {{ request()->routeIs('delivery.review') ? 'bg-gray-200 font-semibold border-l-4 border-green-600' : '' }}">Delivery Review</a>
                            <a href="{{ route('product.management') }}" class="block text-start rounded-lg px-3 py-2 hover:bg-gray-100 text-black {{ request()->routeIs('product.management') ? 'bg-gray-200 font-semibold border-l-4 border-green-600' : '' }}">Product Management</a>
                        </nav>
                    </div>

                    <div class="w-full px-4">
                        <div class="mt-4 flex items-center justify-center">
                            @auth
                                <div class="w-full bg-white text-black rounded-md p-3 shadow-sm">
                                    <p class="text-r font-semibold text-black text-center">Hi, {{ Auth::user()->name }}!</p>

                                    <div class="mt-3 flex items-center justify-center space-x-3">
                                        <a href="{{ route('profile.edit') }}" class="inline-block px-3 py-1 rounded text-sm bg-green-600 text-white hover:bg-green-700">Settings</a>

                                        <form method="POST" action="{{ route('logout') }}" class="inline-block">
                                            @csrf
                                            <button type="submit" class="inline-block px-3 py-1 rounded text-sm bg-red-600 text-white hover:bg-red-700">Logout</button>
                                        </form>
                                    </div>
                                </div>
                            @endauth
                        </div>

                        <footer class="text-center text-xs text-gray-500 mt-6">
                            © {{ date('Y') }} Gemarc Enterprises
                        </footer>
                    </div>
                </aside>

                <!-- Main content area (off-white to contrast with sidebar) -->
                <div class="flex-1 bg-gray-100 text-black">
                    @isset($header)
                        <header class="bg-gray-100 shadow">
                            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    <main class="p-6">
                        @yield('content')
                    </main>
                </div>
            </div>

            @if(session('success'))
                <div class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </body>
</html>
