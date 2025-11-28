@extends('layouts.app')

@section('title', 'Stock Movement | Gemarc LAN Based Inventory System')

@section('content')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Stock Movement</h2>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium mb-4">Stock Movement — Monthly Delivered Items</h3>

                    <p class="text-sm text-gray-600 mb-4">This page shows total delivered (outgoing) quantity per product by month. Source: <span class="font-medium">{{ $source ?? 'none' }}</span></p>

                    <form method="GET" class="mb-4 flex gap-3 items-center">
                        <div>
                            <label class="text-sm text-gray-700">Product</label>
                            <select name="product_id" class="block mt-1 rounded border-gray-300">
                                <option value="">All products</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="pt-6">
                            <button type="submit" class="inline-flex items-center px-3 py-1 bg-blue-600 text-white text-sm rounded">Filter</button>
                            <a href="{{ route('stock.movement') }}" class="ml-2 inline-flex items-center px-3 py-1 bg-gray-200 text-sm rounded">Reset</a>
                        </div>
                    </form>

                    @if($monthly->isEmpty())
                        <div class="p-4 bg-yellow-50 border-l-4 border-yellow-400">
                            <p class="text-sm text-yellow-800">No records found. If you expect delivered (sales) data, add a `deliveries` table with `product_id`, `date`, and `qty` columns. Showing `receivings` is used only as an example when available.</p>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Year</th>
                                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Month</th>
                                        <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Total Delivered</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($monthly as $row)
                                        <tr>
                                            <td class="px-4 py-3 text-sm text-gray-700">{{ $row->product_name ?? '—' }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700">{{ $row->year }}</td>
                                            <td class="px-4 py-3 text-sm text-gray-700">{{ 
                                                \Carbon\Carbon::createFromDate($row->year, $row->month, 1)->format('F')
                                            }}</td>
                                            <td class="px-4 py-3 text-sm text-right text-gray-800 font-semibold">{{ $row->total }}</td>
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
@endsection
