@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="bg-white p-6 rounded shadow max-w-3xl mx-auto">
    <h2 class="text-2xl font-bold mb-4">Edit Product</h2>

    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('product.management.update', $product) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block font-semibold">Part Number</label>
                <input type="text" name="part_number" value="{{ old('part_number', $product->part_number) }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block font-semibold">Inventory ID</label>
                <input type="text" name="inventory_id" value="{{ old('inventory_id', $product->inventory_id) }}" class="w-full border rounded px-3 py-2">
            </div>
            <div class="md:col-span-2">
                <label class="block font-semibold">Name</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" class="w-full border rounded px-3 py-2" required>
            </div>
            <div class="md:col-span-2">
                <label class="block font-semibold">Description</label>
                <textarea name="description" class="w-full border rounded px-3 py-2">{{ old('description', $product->description) }}</textarea>
            </div>
            <div>
                <label class="block font-semibold">Supplier</label>
                <input type="text" name="supplier" value="{{ old('supplier', $product->supplier) }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block font-semibold">F.O #</label>
                <input type="text" name="fo_number" value="{{ old('fo_number', $product->fo_number) }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block font-semibold">Date Received</label>
                <input type="date" name="date_received" value="{{ old('date_received', $product->date_received) }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block font-semibold">Qty. Received</label>
                <input type="number" name="qty_received" value="{{ old('qty_received', $product->qty_received) }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block font-semibold">Unit Price</label>
                <input type="number" step="0.01" name="unit_price" value="{{ old('unit_price', $product->unit_price) }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block font-semibold">Beginning Inventory</label>
                <input type="number" name="beginning_inventory" value="{{ old('beginning_inventory', $product->beginning_inventory) }}" class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block font-semibold">Ending Inventory</label>
                <input type="number" name="ending_inventory" value="{{ old('ending_inventory', $product->ending_inventory) }}" class="w-full border rounded px-3 py-2">
            </div>
            <div class="md:col-span-2">
                <label class="block font-semibold">TOTAL</label>
                <input type="number" step="0.01" name="total" value="{{ old('total', $product->total) }}" class="w-full border rounded px-3 py-2">
            </div>
        </div>

        <div class="mt-4">
            <label class="block font-semibold">Product Image (optional)</label>
            <input type="file" name="image" class="w-full">
            @if($product->image_path)
                <div class="mt-3">
                    <p class="text-sm text-gray-600">Current image:</p>
                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="Current image" class="h-24 mt-2">
                </div>
            @endif
        </div>

        <div class="mt-6 flex space-x-3">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Save</button>
            <a href="{{ route('product.management') }}" class="inline-block px-4 py-2 border rounded">Cancel</a>
        </div>
    </form>
</div>
@endsection
