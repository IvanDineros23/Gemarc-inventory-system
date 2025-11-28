@extends('layouts.app')

@section('title', 'Delivery Entry | Gemarc LAN Based Inventory System')

@section('content')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Delivery Entry</h2>
    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if(session('success'))
                        <div class="mb-4 p-3 bg-green-50 border-l-4 border-green-400 text-green-800">{{ session('success') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="mb-4 p-3 bg-red-50 border-l-4 border-red-400 text-red-800">
                            <ul class="list-disc pl-5">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('delivery.store') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm text-gray-700">Product</label>
                            <select name="product_id" class="block w-full mt-1 rounded border-gray-300" required>
                                <option value="">-- choose product --</option>
                                @foreach($products as $p)
                                    <option value="{{ $p->id }}" {{ old('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-700">Date / Time</label>
                            <input type="datetime-local" name="date" value="{{ old('date') }}" class="block w-full mt-1 rounded border-gray-300">
                        </div>

                        <div>
                            <label class="block text-sm text-gray-700">Quantity</label>
                            <input type="number" name="qty" min="1" value="{{ old('qty',1) }}" class="block w-48 mt-1 rounded border-gray-300" required>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-700">Remarks (optional)</label>
                            <textarea name="remarks" class="block w-full mt-1 rounded border-gray-300" rows="3">{{ old('remarks') }}</textarea>
                        </div>

                        <div class="pt-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded">Record Delivery</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
@endsection
