{{-- resources/views/layouts/guest.blade.php --}}
@props(['title' => config('app.name', 'Laravel')])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Gemarc LAN Based Inventory System</title>
    <link rel="icon" href="{{ asset('images/gemarclogo.png') }}" type="image/png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900">
    {{-- MAIN CONTENT SLOT --}}
    <div class="min-h-screen">
        {{ $slot }}
    </div>

    @if(session('success'))
        <div class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow">
            {{ session('success') }}
        </div>
    @endif
</body>
</html>
