@extends('layouts.app')

@section('title', 'Re-order Level Entry | Gemarc LAN Based Inventory System')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Re-order Level Entry</h3>

                    {{-- Notification banner when there are low-stock fast-moving items --}}
                    @php
                        $lowFast = $lowFastMoving ?? collect();
                        $lowAll = $lowStock ?? collect();
                        $threshold = $lowStockThreshold ?? 5;
                    @endphp

                    @if($lowFast->count() > 0)
                        <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-500">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="font-semibold text-red-700">Critical: Low stock on fast-moving items</p>
                                    <p class="text-sm text-red-600">There are {{ $lowFast->count() }} fast-moving item(s) at or below {{ $threshold }} units. Please prioritize replenishment.</p>
                                </div>
                                <div>
                                    <a href="#low-stock-list" class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-sm rounded">View</a>
                                </div>
                            </div>
                        </div>
                    @elseif($lowAll->count() > 0)
                        <div class="mb-4 p-4 bg-yellow-50 border-l-4 border-yellow-400">
                            <p class="font-semibold text-yellow-800">Notice: Low stock items</p>
                            <p class="text-sm text-yellow-700">There are {{ $lowAll->count() }} item(s) at or below {{ $threshold }} units.</p>
                        </div>
                    @else
                        <div class="mb-4 p-4 bg-green-50 border-l-4 border-green-400">
                            <p class="font-semibold text-green-700">All good</p>
                            <p class="text-sm text-green-600">No items are currently at or below {{ $threshold }} units.</p>
                        </div>
                    @endif

                    {{-- Low stock list --}}
                    <div id="low-stock-list" class="mt-6">
                        <h4 class="font-semibold mb-2">Low Stock Items</h4>
                        @if($lowAll->isEmpty())
                            <p class="text-sm text-gray-600">No low stock items to display.</p>
                        @else
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200">
                                    <thead class="bg-gray-50">
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Part #</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Ending Inventory</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Qty Received</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-gray-200">
                                        @foreach($lowAll as $prod)
                                            @php
                                                $isFast = ($prod->qty_received !== null && $prod->qty_received >= 20);
                                            @endphp
                                            <tr class="{{ $isFast ? 'bg-red-50' : '' }}">
                                                <td class="px-4 py-3 text-sm text-gray-700">{{ $prod->part_number ?? '-' }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-700">{{ $prod->name }}</td>
                                                <td class="px-4 py-3 text-sm font-semibold {{ $prod->ending_inventory <= $threshold ? 'text-red-700' : 'text-gray-700' }}">{{ $prod->ending_inventory }}</td>
                                                <td class="px-4 py-3 text-sm text-gray-700">{{ $prod->qty_received ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
