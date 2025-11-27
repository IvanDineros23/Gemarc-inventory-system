@extends('layouts.app')

@section('title', 'Inventory Report | Gemarc LAN Based Inventory System')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-2xl font-bold">Inventory Report</h2>
                    <a href="{{ route('inventory.report.download') }}" class="px-3 py-2 bg-green-600 text-white rounded">Print</a>
                </div>
                <div class="mb-8">
                    <table class="min-w-full text-lg">
                        <thead>
                            <tr class="text-left text-gray-700 border-b">
                                <th class="px-4 py-2">Brand</th>
                                <th class="px-4 py-2 text-right">Grand Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($brands as $brand)
                                <tr class="border-b">
                                    <td class="px-4 py-3 font-semibold">{{ $brand['name'] }}</td>
                                    <td class="px-4 py-3 text-right">₱ {{ number_format($brand['total'], 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-8 text-right text-lg font-bold print:text-xl">
                    Grand Total: ₱ <span>{{ number_format($grandTotal, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
