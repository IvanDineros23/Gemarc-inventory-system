@extends('layouts.app')

@section('title', 'Receiving Entry | Gemarc LAN Based Inventory System')

@section('content')
    <!-- Manual Receiving Entry Button -->
    <div class="flex justify-end mb-4">
        <button id="manualReceivingBtn" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Manual Receiving Entry
        </button>
    </div>

    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-2 lg:px-0">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @php
                        $products = $products ?? collect();
                    @endphp

                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold">Receiving Entry — Input incoming delivery</h2>
                        <div class="flex items-center space-x-2">
                            <button type="button"
                                    class="btn-add-new-item inline-block bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">
                                Add new item
                            </button>
                            <button id="exportReceivingBtn"
                                    class="inline-block bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700">
                                Export Receiving Report
                            </button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <input type="text" id="receivingSearch"
                               class="border rounded px-3 py-2 w-full max-w-md"
                               placeholder="Search products..."
                               oninput="filterReceivingProducts()">
                    </div>

                    <div class="overflow-x-visible">
                    <table class="w-full text-sm border border-gray-300" id="receivingTable">
                        <thead class="bg-gray-700 text-white">
                            <tr>
                                <th class="px-3 py-2 text-left">Part Number</th>
                                <th class="px-3 py-2 text-left">Inventory ID</th>
                                <th class="px-3 py-2 text-left">Name</th>
                                <th class="px-3 py-2 text-left">Supplier</th>
                                <th class="px-3 py-2 text-center whitespace-nowrap">Qty On Hand</th>
                                <th class="px-3 py-2 text-center">Action</th>
                            </tr>
                        </thead>
                            <tbody id="receivingTableBody">
                                @forelse($products as $product)
                                    <tr class="border-b hover:bg-gray-50" data-product-id="{{ $product->id }}">
                                        <td class="px-2 py-2 truncate text-center">{{ $product->part_number }}</td>
                                        <td class="px-2 py-2 truncate text-center">{{ $product->inventory_id }}</td>
                                        <td class="px-2 py-2 whitespace-normal break-words text-center">
                                            {{ $product->name }}
                                        </td>
                                        <td class="px-2 py-2 truncate text-center">{{ $product->supplier }}</td>
                                        <td class="px-2 py-2 text-center">
                                            {{ $product->ending_inventory ?? 0 }}
                                        </td>
                                        <td class="px-2 py-2 text-center whitespace-nowrap">
                                            <div class="inline-flex items-center gap-2">
                                                <button type="button"
                                                        class="px-3 py-1 border rounded receive-btn"
                                                        data-product='@json($product)'>
                                                    Receive
                                                </button>
                                                <button type="button"
                                                        class="px-3 py-1 border rounded delete-product-btn"
                                                        data-product-id="{{ $product->id }}"
                                                        data-product-name="{{ $product->name }}">
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-gray-500 py-4">
                                            No products found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- RECEIVED ITEMS (linked to products) --}}
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-2">Received Items</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm table-fixed border">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-2 py-2 text-left">Date</th>
                                        <th class="px-2 py-2 text-left">Part / Item</th>
                                        <th class="px-2 py-2 text-left">Supplier</th>
                                        <th class="px-2 py-2 text-right">Qty</th>
                                        <th class="px-2 py-2 text-right">Unit Price</th>
                                        <th class="px-2 py-2 text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="receivedItemsTableBody">
                                    @if(isset($receivedItems) && $receivedItems->count())
                                        @foreach($receivedItems as $r)
                                            <tr class="border-t" data-receiving-id="{{ $r->id }}">
                                                <td class="px-2 py-2">{{ $r->date_received ?? $r->created_at->format('Y-m-d') }}</td>
                                                <td class="px-2 py-2">{{ $r->product->part_number ?? '' }} {{ $r->product ? '— ' . $r->product->name : '' }}</td>
                                                <td class="px-2 py-2">{{ $r->product->supplier ?? '' }}</td>
                                                <td class="px-2 py-2 text-right">{{ $r->qty_received ?? '' }}</td>
                                                <td class="px-2 py-2 text-right">{{ $r->unit_price ? number_format($r->unit_price,2) : '' }}</td>
                                                <td class="px-2 py-2 text-center">
                                                    <button type="button" class="px-3 py-1 border rounded edit-receiving-btn" data-receiving='@json($r)'>Edit</button>
                                                    <button type="button" class="px-3 py-1 border rounded ml-2 delete-receiving-btn" data-receiving-id="{{ $r->id }}">Delete</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="px-2 py-4 text-center text-gray-500">No received items yet.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- MANUAL RECEIVINGS LIST --}}
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-2">Manual Receivings</h3>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm table-fixed border">
                                <thead class="bg-gray-100">
                                    <tr>
                                        <th class="px-2 py-2 text-left">Date</th>
                                        <th class="px-2 py-2 text-left">Part / Item</th>
                                        <th class="px-2 py-2 text-left">Supplier</th>
                                        <th class="px-2 py-2 text-right">Qty</th>
                                        <th class="px-2 py-2 text-right">Unit Price</th>
                                        <th class="px-2 py-2 text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(isset($manualReceivings) && $manualReceivings->count())
                                        @foreach($manualReceivings as $m)
                                            <tr class="border-t" data-receiving-id="{{ $m->id }}" data-receiving='@json($m)'>
                                                <td class="px-2 py-2">{{ $m->date_received ?? $m->created_at->format('Y-m-d') }}</td>
                                                <td class="px-2 py-2">{{ $m->product->part_number ?? ($m->part_number ?? '') }} {{ $m->product ? '— ' . $m->product->name : '' }}</td>
                                                <td class="px-2 py-2">{{ $m->product->supplier ?? ($m->supplier ?? '') }}</td>
                                                <td class="px-2 py-2 text-right">{{ $m->qty_received ?? '' }}</td>
                                                <td class="px-2 py-2 text-right">{{ $m->unit_price ? number_format($m->unit_price,2) : '' }}</td>
                                                <td class="px-2 py-2 text-center whitespace-nowrap">
                                                    <div class="inline-flex items-center gap-2">
                                                        <button type="button" class="px-3 py-1 border rounded edit-receiving-btn" data-receiving='@json($m)'>Edit</button>
                                                        <button type="button" class="px-3 py-1 border rounded delete-receiving-btn" data-receiving-id="{{ $m->id }}">Delete</button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="6" class="px-2 py-4 text-center text-gray-500">No manual receivings yet.</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>

                      <p class="text-sm text-gray-600 mt-4">
                        Click <strong>Receive</strong> on a product to open the receiving details form
                        (you can attach a details inventory Excel file there).
                    </p>

                    {{-- RECEIVING MODAL (per product) --}}
                    <div id="receiveModal"
                         class="fixed inset-0 bg-black bg-opacity-40 hidden z-50">
                        <div class="min-h-screen flex items-start md:items-center justify-center p-4">
                            <div class="bg-white rounded shadow-lg w-11/12 md:w-3/4 lg:w-2/3 p-6 relative max-h-[90vh] overflow-y-auto">
                                <button id="closeReceiveModal" class="absolute top-3 right-3 text-gray-600">✕</button>
                                <h3 class="text-lg font-semibold mb-4">Receiving Details</h3>

                                <form id="receiveForm" action="{{ route('receiving.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="product_id" id="receive_product_id">

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block font-semibold">Part Number</label>
                                            <input type="text" id="receive_part_number"
                                                   class="w-full border rounded px-3 py-2" readonly>
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Inventory ID</label>
                                            <input type="text" id="receive_inventory_id"
                                                   class="w-full border rounded px-3 py-2" readonly>
                                        </div>
                                        <div>
                                            <label class="block font-semibold">F.O #</label>
                                            <input type="text" name="fo_number"
                                                   class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Date Received</label>
                                            <input type="date" name="date_received"
                                                   class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Qty. Received</label>
                                            <input type="number" name="qty_received"
                                                   class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Unit Price</label>
                                            <input type="number" step="0.01" name="unit_price"
                                                   class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Beginning Inventory</label>
                                            <input type="number" name="beginning_inventory"
                                                   class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Ending Inventory</label>
                                            <input type="number" name="ending_inventory"
                                                   class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block font-semibold">
                                                Details Inventory Excel File (optional)
                                            </label>
                                            <input type="file" name="details_file"
                                                   accept=".xlsx,.xls,.csv"
                                                   class="w-full">
                                        </div>
                                    </div>

                                    <div class="mt-4 flex gap-3">
                                        <button type="submit"
                                                class="bg-indigo-600 text-white px-4 py-2 rounded">
                                            Save Receiving
                                        </button>
                                        <button type="button" id="cancelReceive"
                                                class="px-4 py-2 border rounded">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- EDIT RECEIVING MODAL --}}
                    <div id="editReceivingModal"
                         class="fixed inset-0 bg-black bg-opacity-40 hidden z-50">
                        <div class="min-h-screen flex items-start md:items-center justify-center p-4">
                            <div class="bg-white rounded shadow-lg w-11/12 md:w-3/4 lg:w-2/3 p-6 relative max-h-[90vh] overflow-y-auto">
                                <button id="closeEditReceivingModal" class="absolute top-3 right-3 text-gray-600">✕</button>
                                <h3 class="text-lg font-semibold mb-4">Edit Receiving</h3>

                                <form id="editReceivingForm" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" id="edit_receiving_id" name="receiving_id" value="">

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block font-semibold">Part Number</label>
                                            <input type="text" id="edit_part_number" name="part_number" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Item Name</label>
                                            <input type="text" id="edit_item_name" name="item_name" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Supplier</label>
                                            <input type="text" id="edit_supplier" name="supplier" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">F.O #</label>
                                            <input type="text" id="edit_fo_number" name="fo_number" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Date Received</label>
                                            <input type="date" id="edit_date_received" name="date_received" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Qty. Received</label>
                                            <input type="number" id="edit_qty_received" name="qty_received" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Unit Price</label>
                                            <input type="number" step="0.01" id="edit_unit_price" name="unit_price" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Beginning Inventory</label>
                                            <input type="number" id="edit_beginning_inventory" name="beginning_inventory" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Ending Inventory</label>
                                            <input type="number" id="edit_ending_inventory" name="ending_inventory" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block font-semibold">Replace Details File (optional)</label>
                                            <input type="file" id="edit_details_file" name="details_file" accept=".xlsx,.xls,.csv" class="w-full">
                                        </div>
                                    </div>

                                    <div class="mt-4 flex gap-3">
                                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded">Save Changes</button>
                                        <button type="button" id="cancelEditReceiving" class="px-4 py-2 border rounded">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- MANUAL RECEIVING MODAL --}}
                    <div id="manualReceivingModal"
                         class="fixed inset-0 bg-black bg-opacity-40 hidden z-50">
                        <div class="min-h-screen flex items-start md:items-center justify-center p-4">
                            <div id="manualReceivingModalContent"
                                 class="bg-white rounded shadow-lg w-full max-w-3xl p-6 relative max-h-[90vh] overflow-y-auto"
                                 style="box-shadow: 0 8px 32px rgba(0,0,0,0.25);">
                                <button id="closeManualReceivingModal"
                                        class="absolute top-3 right-3 text-gray-600">✕</button>
                                <h3 class="text-lg font-semibold mb-4 text-center">Receiving Entry</h3>

                                <form id="manualReceivingForm" action="{{ route('receiving.manual') }}" method="POST" enctype="multipart/form-data" class="w-full">
                                    @csrf
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block font-semibold">Part Number:</label>
                                            <input type="text" name="part_number" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Item Name:</label>
                                            <input type="text" name="item_name" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block font-semibold">Item Description:</label>
                                            <input type="text" name="item_description" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Supplier:</label>
                                            <input type="text" name="supplier" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Supplier Group:</label>
                                            <input type="text" name="supplier_group" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Date Received:</label>
                                            <input type="date" name="date_received" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">F.O Number:</label>
                                            <input type="text" name="fo_number" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Quantity:</label>
                                            <input type="number" name="quantity" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Unit Cost:</label>
                                            <input type="number" step="0.01" name="unit_cost" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Unit:</label>
                                            <input type="text" name="unit" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Currency:</label>
                                            <input type="text" name="currency" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block font-semibold">Location:</label>
                                            <textarea name="location" class="w-full border rounded px-3 py-2" rows="2"></textarea>
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block font-semibold">Intended to:</label>
                                            <textarea name="intended_to" class="w-full border rounded px-3 py-2" rows="2"></textarea>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex gap-3 justify-end">
                                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                                            Save Entry
                                        </button>
                                        <button type="button" id="cancelManualReceiving" class="px-4 py-2 border rounded">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- ADD NEW ITEM MODAL --}}
                    <div id="addNewItemModal"
                         class="fixed inset-0 bg-black bg-opacity-40 hidden z-50">
                        <div class="min-h-screen flex items-start md:items-center justify-center p-4">
                            <div class="bg-white rounded shadow-lg w-full max-w-3xl p-6 relative max-h-[90vh] overflow-y-auto"
                                 style="box-shadow: 0 8px 32px rgba(0,0,0,0.25);">
                                <button id="closeAddNewItemModal"
                                        class="absolute top-3 right-3 text-gray-600">✕</button>
                                <h3 class="text-lg font-semibold mb-4 text-center">Add New Item</h3>

                                <form id="addNewItemForm"
                                      action="{{ route('product.management.store') }}"
                                      method="POST" enctype="multipart/form-data" class="w-full">
                                    @csrf
                                    <input type="hidden" name="redirect_to" value="receiving">

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block font-semibold">Part Number:</label>
                                            <input type="text" name="part_number" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Inventory ID:</label>
                                            <input type="text" name="inventory_id" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Name:</label>
                                            <input type="text" name="name" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Description:</label>
                                            <textarea name="description" class="w-full border rounded px-3 py-2"></textarea>
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Supplier:</label>
                                            <input type="text" name="supplier" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">F.O #:</label>
                                            <input type="text" name="fo_number" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Date Received:</label>
                                            <input type="date" name="date_received" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Qty. Received:</label>
                                            <input type="number" name="qty_received" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Unit Price:</label>
                                            <input type="number" step="0.01" name="unit_price" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Beginning Inventory:</label>
                                            <input type="number" name="beginning_inventory" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Ending Inventory:</label>
                                            <input type="number" name="ending_inventory" class="w-full border rounded px-3 py-2">
                                        </div>
                                        <div>
                                            <label class="block font-semibold">TOTAL:</label>
                                            <input type="number" step="0.01" name="total"
                                                   class="w-full border rounded px-3 py-2" readonly>
                                        </div>
                                        <div>
                                            <label class="block font-semibold">Product Image (optional):</label>
                                            <input type="file" name="image"
                                                   class="w-full border rounded px-3 py-2">
                                        </div>
                                    </div>

                                    <div class="mt-4 flex gap-3 justify-end">
                                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">
                                            Save Item
                                        </button>
                                        <button type="button" id="cancelAddNewItem" class="px-4 py-2 border rounded">
                                            Cancel
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- DELETE CONFIRMATION MODAL (for products) --}}
                    <div id="productDeleteModal" class="fixed inset-0 bg-black bg-opacity-40 hidden z-50">
                        <div class="min-h-screen flex items-start md:items-center justify-center p-4">
                            <div class="bg-white rounded shadow-lg w-full max-w-lg p-6 relative">
                                <button id="closeProductDeleteModal" class="absolute top-3 right-3 text-gray-600">✕</button>
                                <h3 class="text-lg font-semibold mb-4">Confirm Delete</h3>
                                <p id="productDeleteMessage" class="mb-4 text-gray-700">Are you sure you want to delete this product?</p>
                                <div class="flex justify-end gap-2">
                                    <button id="cancelProductDelete" class="px-4 py-2 border rounded">Cancel</button>
                                    <button id="confirmProductDelete" class="px-4 py-2 bg-red-600 text-white rounded">Delete</button>
                                </div>
                            </div>
                        </div>
                    </div>

                </div> {{-- .p-6 --}}
            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script>
        // Live search (debounced AJAX) — updates table in-place without reloading
        let __receiving_search_timeout = null;
        function filterReceivingProducts() {
            const q = document.getElementById('receivingSearch').value.trim();
            clearTimeout(__receiving_search_timeout);
            __receiving_search_timeout = setTimeout(() => {
                const url = '{{ route('product.search') }}?q=' + encodeURIComponent(q);
                fetch(url, { headers: { 'Accept': 'application/json' } })
                    .then(resp => resp.json())
                    .then(data => {
                        const tbody = document.getElementById('receivingTableBody');
                        tbody.innerHTML = '';
                        if (!data || data.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="6" class="text-center text-gray-500 py-4">No products found.</td></tr>';
                            return;
                        }

                        data.forEach(p => {
                            const tr = document.createElement('tr');
                            tr.className = 'border-b hover:bg-gray-50';

                            const tdPart = document.createElement('td');
                            tdPart.className = 'px-2 py-2 truncate text-center';
                            tdPart.textContent = p.part_number || '';

                            const tdInv = document.createElement('td');
                            tdInv.className = 'px-2 py-2 truncate text-center';
                            tdInv.textContent = p.inventory_id || '';

                            const tdName = document.createElement('td');
                            tdName.className = 'px-2 py-2 whitespace-normal break-words text-center';
                            tdName.textContent = p.name || '';

                            const tdSup = document.createElement('td');
                            tdSup.className = 'px-2 py-2 truncate text-center';
                            tdSup.textContent = p.supplier || '';

                            const tdQty = document.createElement('td');
                            tdQty.className = 'px-2 py-2 text-center';
                            tdQty.textContent = (p.ending_inventory !== null && p.ending_inventory !== undefined) ? p.ending_inventory : 0;

                            const tdAct = document.createElement('td');
                            tdAct.className = 'px-2 py-2 text-center whitespace-nowrap';
                            const wrap = document.createElement('div');
                            wrap.className = 'inline-flex items-center gap-2';
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'px-3 py-1 border rounded receive-btn';
                            btn.textContent = 'Receive';
                            btn.dataset.product = JSON.stringify(p);
                            const delBtn = document.createElement('button');
                            delBtn.type = 'button';
                            delBtn.className = 'px-3 py-1 border rounded delete-product-btn';
                            delBtn.textContent = 'Delete';
                            delBtn.dataset.productId = p.id;
                            wrap.appendChild(btn);
                            wrap.appendChild(delBtn);
                            tdAct.appendChild(wrap);

                            tr.appendChild(tdPart);
                            tr.appendChild(tdInv);
                            tr.appendChild(tdName);
                            tr.appendChild(tdSup);
                            tr.appendChild(tdQty);
                            tr.appendChild(tdAct);

                            // set data attribute for easier lookup when removing after receive
                            tr.dataset.productId = p.id;
                            tbody.appendChild(tr);
                        });

                        // reattach receive button handlers (defined during DOMContentLoaded)
                        if (typeof window._attachReceiveHandlers === 'function') {
                            window._attachReceiveHandlers();
                        }
                        if (typeof window._attachProductDeleteHandlers === 'function') {
                            window._attachProductDeleteHandlers();
                        }
                    }).catch(() => {
                        // silently ignore fetch errors for now
                    });
            }, 300);
        }

        document.addEventListener('DOMContentLoaded', () => {
            const body = document.body;

            // Helper open/close
            function openModal(el) {
                if (!el) return;
                el.classList.remove('hidden');
                body.style.overflow = 'hidden';
            }
            function closeModal(el) {
                if (!el) return;
                el.classList.add('hidden');
                body.style.overflow = '';
            }

            // ===== Manual Receiving Modal =====
            const manualBtn   = document.getElementById('manualReceivingBtn');
            const manualModal = document.getElementById('manualReceivingModal');
            const manualClose = document.getElementById('closeManualReceivingModal');
            const manualCancel= document.getElementById('cancelManualReceiving');

            if (manualBtn && manualModal) {
                manualBtn.addEventListener('click', () => openModal(manualModal));
                manualClose.addEventListener('click', () => closeModal(manualModal));
                manualCancel.addEventListener('click', () => closeModal(manualModal));
                manualModal.addEventListener('click', e => {
                    if (e.target === manualModal) closeModal(manualModal);
                });
            }

            // ===== Add New Item Modal =====
            const addModal        = document.getElementById('addNewItemModal');
            const addClose        = document.getElementById('closeAddNewItemModal');
            const addCancel       = document.getElementById('cancelAddNewItem');

            document.addEventListener('click', e => {
                const btn = e.target.closest('.btn-add-new-item');
                if (btn && addModal) {
                    e.preventDefault();
                    openModal(addModal);
                }
            });
            if (addModal) {
                addClose.addEventListener('click', () => closeModal(addModal));
                addCancel.addEventListener('click', () => closeModal(addModal));
                addModal.addEventListener('click', e => {
                    if (e.target === addModal) closeModal(addModal);
                });
            }

            // AJAX submit for Add New Item form so new product appears instantly
            const addNewItemForm = document.getElementById('addNewItemForm');
            if (addNewItemForm) {
                addNewItemForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const url = addNewItemForm.action;
                    const fd = new FormData(addNewItemForm);
                    try {
                        const resp = await fetch(url, {
                            method: 'POST',
                            body: fd,
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });

                        if (!resp.ok) {
                            const data = await resp.json().catch(()=>null);
                            alert('Failed to save item' + (data && data.message ? ': '+data.message : ''));
                            return;
                        }

                        const product = await resp.json();
                        // close modal
                        closeModal(addModal);

                        // append to receiving table
                        const tbody = document.getElementById('receivingTableBody');
                        if (tbody) {
                            const tr = document.createElement('tr');
                            tr.className = 'border-b hover:bg-gray-50';

                            const tdPart = document.createElement('td'); tdPart.className = 'px-2 py-2 truncate text-center'; tdPart.textContent = product.part_number || '';
                            const tdInv = document.createElement('td'); tdInv.className = 'px-2 py-2 truncate text-center'; tdInv.textContent = product.inventory_id || '';
                            const tdName = document.createElement('td'); tdName.className = 'px-2 py-2 whitespace-normal break-words text-center'; tdName.textContent = product.name || '';
                            const tdSup = document.createElement('td'); tdSup.className = 'px-2 py-2 truncate text-center'; tdSup.textContent = product.supplier || '';
                            const tdQty = document.createElement('td'); tdQty.className = 'px-2 py-2 text-center'; tdQty.textContent = product.ending_inventory ?? 0;
                            const tdAct = document.createElement('td'); tdAct.className = 'px-2 py-2 text-center whitespace-nowrap';
                            const wrapAct = document.createElement('div'); wrapAct.className = 'inline-flex items-center gap-2';
                            const btn = document.createElement('button'); btn.type='button'; btn.className='px-3 py-1 border rounded receive-btn'; btn.textContent='Receive'; btn.dataset.product = JSON.stringify(product);
                            const delBtn = document.createElement('button'); delBtn.type='button'; delBtn.className='px-3 py-1 border rounded delete-product-btn'; delBtn.textContent='Delete'; delBtn.dataset.productId = product.id;
                            wrapAct.appendChild(btn);
                            wrapAct.appendChild(delBtn);
                            tdAct.appendChild(wrapAct);

                            tr.appendChild(tdPart); tr.appendChild(tdInv); tr.appendChild(tdName); tr.appendChild(tdSup); tr.appendChild(tdQty); tr.appendChild(tdAct);
                            // insert at top
                            if (tbody.firstChild) tbody.insertBefore(tr, tbody.firstChild);
                            else tbody.appendChild(tr);

                            // attach handlers
                            if (typeof window._attachReceiveHandlers === 'function') window._attachReceiveHandlers();
                            if (typeof window._attachProductDeleteHandlers === 'function') window._attachProductDeleteHandlers();
                        }

                        // refresh dashboard summary cards if present
                        try {
                            const sumResp = await fetch('{{ route('api.dashboard.summary') }}', { headers:{ 'Accept':'application/json' } });
                            if (sumResp.ok) {
                                const sum = await sumResp.json();
                                const tp = document.getElementById('card-total-products'); if (tp) tp.textContent = sum.total_products;
                                const tv = document.getElementById('card-total-stock'); if (tv) tv.textContent = new Intl.NumberFormat('en-PH', { style:'currency', currency:'PHP' }).format(sum.total_stock_value || 0);
                                const tl = document.getElementById('card-low-stock'); if (tl) tl.textContent = sum.low_stock_count;
                            }
                        } catch (e) { /* ignore */ }

                    } catch (err) {
                        console.error('Add item failed', err);
                        alert('Failed to save item');
                    }
                });
            }

            // ===== Receive Modal (per product) =====
            const receiveModal = document.getElementById('receiveModal');
            const receiveClose = document.getElementById('closeReceiveModal');
            const receiveCancel= document.getElementById('cancelReceive');

            // Attach handlers for receive buttons. Exposed as window._attachReceiveHandlers so
            // dynamically rendered rows (from live-search) can rebind easily.
            window._attachReceiveHandlers = function () {
                document.querySelectorAll('.receive-btn').forEach(btn => {
                    // avoid double-binding by checking a stored flag on the element
                    if (btn._hasReceiveHandler) return;
                    const handler = () => {
                        let product = null;
                        try { product = JSON.parse(btn.dataset.product); } catch (e) { product = null; }
                        if (product) {
                            document.getElementById('receive_product_id').value = product.id ?? '';
                            document.getElementById('receive_part_number').value = product.part_number ?? '';
                            document.getElementById('receive_inventory_id').value = product.inventory_id ?? '';
                        }
                        openModal(receiveModal);
                    };
                    btn.addEventListener('click', handler);
                    btn._hasReceiveHandler = true;
                });
            };

            // attach initially present rows
            window._attachReceiveHandlers();

            if (receiveModal) {
                receiveClose.addEventListener('click', () => closeModal(receiveModal));
                receiveCancel.addEventListener('click', () => closeModal(receiveModal));
                receiveModal.addEventListener('click', e => {
                    if (e.target === receiveModal) closeModal(receiveModal);
                });
            }

            // ===== Edit Receiving Modal handlers =====
            const editModal = document.getElementById('editReceivingModal');
            const editClose = document.getElementById('closeEditReceivingModal');
            const editCancel = document.getElementById('cancelEditReceiving');

            if (editModal) {
                if (editClose) editClose.addEventListener('click', () => closeModal(editModal));
                if (editCancel) editCancel.addEventListener('click', () => closeModal(editModal));
                editModal.addEventListener('click', e => { if (e.target === editModal) closeModal(editModal); });
            }

            // Attach handlers for edit buttons
            window._attachEditHandlers = function () {
                document.querySelectorAll('.edit-receiving-btn').forEach(btn => {
                    if (btn._hasEditHandler) return;
                    const handler = () => {
                        let rec = null;
                        try { rec = JSON.parse(btn.dataset.receiving); } catch (e) { rec = null; }
                        if (!rec) return;
                        document.getElementById('edit_receiving_id').value = rec.id || '';
                        document.getElementById('edit_part_number').value = rec.part_number ?? (rec.product ? (rec.product.part_number ?? '') : '');
                        document.getElementById('edit_item_name').value = rec.item_name ?? (rec.product ? (rec.product.name ?? '') : '');
                        document.getElementById('edit_supplier').value = rec.supplier ?? (rec.product ? (rec.product.supplier ?? '') : '');
                        document.getElementById('edit_fo_number').value = rec.fo_number ?? '';
                        document.getElementById('edit_date_received').value = rec.date_received ? rec.date_received.substring(0,10) : '';
                        document.getElementById('edit_qty_received').value = rec.qty_received ?? '';
                        document.getElementById('edit_unit_price').value = rec.unit_price ?? '';
                        document.getElementById('edit_beginning_inventory').value = rec.beginning_inventory ?? '';
                        document.getElementById('edit_ending_inventory').value = rec.ending_inventory ?? '';
                        // clear file input
                        const fileInput = document.getElementById('edit_details_file'); if (fileInput) fileInput.value = null;
                        openModal(editModal);
                    };
                    btn.addEventListener('click', handler);
                    btn._hasEditHandler = true;
                });
            };

            // attach any existing edit buttons
            window._attachEditHandlers();
            // attach delete handlers
            window._attachDeleteHandlers = function () {
                document.querySelectorAll('.delete-receiving-btn').forEach(btn => {
                    if (btn._hasDeleteHandler) return;
                    btn.addEventListener('click', async () => {
                        const id = btn.dataset.receivingId;
                        if (!id) return;
                        if (!confirm('Delete this receiving entry? This cannot be undone.')) return;
                        try {
                            const resp = await fetch('{{ url('/receiving') }}/' + id, {
                                method: 'DELETE',
                                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept':'application/json' }
                            });
                            if (!resp.ok) {
                                alert('Failed to delete');
                                return;
                            }
                            // remove row from DOM
                            const tr = document.querySelector('[data-receiving-id="'+id+'"]');
                            if (tr) tr.remove();

                            // refresh dashboard summary
                            try {
                                const sumResp = await fetch('{{ route('api.dashboard.summary') }}', { headers:{ 'Accept':'application/json' } });
                                if (sumResp.ok) {
                                    const sum = await sumResp.json();
                                    const tp = document.getElementById('card-total-products'); if (tp) tp.textContent = sum.total_products;
                                    const tv = document.getElementById('card-total-stock'); if (tv) tv.textContent = new Intl.NumberFormat('en-PH', { style:'currency', currency:'PHP' }).format(sum.total_stock_value || 0);
                                    const tl = document.getElementById('card-low-stock'); if (tl) tl.textContent = sum.low_stock_count;
                                }
                            } catch (e) { /* ignore */ }

                        } catch (err) {
                            console.error('Delete failed', err);
                            alert('Failed to delete');
                        }
                    });
                    btn._hasDeleteHandler = true;
                });
            };
            window._attachDeleteHandlers();

            

            // ===== Product delete handlers for top product list =====
            window._attachProductDeleteHandlers = function () {
                document.querySelectorAll('.delete-product-btn').forEach(btn => {
                    if (btn._hasProductDeleteHandler) return;
                    btn.addEventListener('click', async () => {
                        const id = btn.dataset.productId;
                        if (!id) return;
                        if (!confirm('Delete this product? This will remove it from the product list.')) return;
                        try {
                            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
                            const headers = { 'X-Requested-With': 'XMLHttpRequest', 'Accept':'application/json' };
                            if (tokenMeta) headers['X-CSRF-TOKEN'] = tokenMeta.content;

                            const resp = await fetch('{{ url('/product-management') }}/' + id, {
                                method: 'DELETE',
                                headers: headers
                            });
                            if (!resp.ok) {
                                alert('Failed to delete product');
                                return;
                            }
                            // remove product row from top list
                            const tr = document.querySelector('[data-product-id="'+id+'"]');
                            if (tr) tr.remove();

                            // also remove from search/live results if present
                            document.querySelectorAll('[data-product-id="'+id+'"]').forEach(e => e.remove());

                            // refresh dashboard summary
                            try {
                                const sumResp = await fetch('{{ route('api.dashboard.summary') }}', { headers:{ 'Accept':'application/json' } });
                                if (sumResp.ok) {
                                    const sum = await sumResp.json();
                                    const tp = document.getElementById('card-total-products'); if (tp) tp.textContent = sum.total_products;
                                    const tv = document.getElementById('card-total-stock'); if (tv) tv.textContent = new Intl.NumberFormat('en-PH', { style:'currency', currency:'PHP' }).format(sum.total_stock_value || 0);
                                    const tl = document.getElementById('card-low-stock'); if (tl) tl.textContent = sum.low_stock_count;
                                }
                            } catch (e) { /* ignore */ }

                        } catch (err) {
                            console.error('Product delete failed', err);
                            alert('Failed to delete product');
                        }
                    });
                    btn._hasProductDeleteHandler = true;
                });
            };
            window._attachProductDeleteHandlers();

            // ===== Edit form submit via AJAX =====
            const editForm = document.getElementById('editReceivingForm');
            if (editForm) {
                editForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const id = document.getElementById('edit_receiving_id').value;
                    if (!id) return alert('Invalid receiving id');
                    const fd = new FormData(editForm);
                    fd.append('_method', 'PUT');
                    try {
                        const resp = await fetch('{{ url('/receiving') }}/' + id, {
                            method: 'POST',
                            body: fd,
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });

                        if (!resp.ok) {
                            const data = await resp.json().catch(()=>null);
                            const msg = data && data.errors ? Object.values(data.errors).flat().join('\n') : 'Failed to update receiving';
                            alert(msg);
                            return;
                        }

                        const updated = await resp.json().catch(()=>null);
                        closeModal(editModal);

                        // update row in Received Items table
                        try {
                            const tr = document.querySelector('[data-receiving-id="'+id+'"]');
                            if (tr && updated) {
                                tr.children[0].textContent = updated.date_received || (new Date()).toISOString().slice(0,10);
                                if (updated.product) {
                                    tr.children[1].textContent = (updated.product.part_number ? updated.product.part_number : '') + (updated.product.name ? ' — ' + updated.product.name : '');
                                    tr.children[2].textContent = updated.product.supplier ?? '';
                                } else {
                                    tr.children[1].textContent = (updated.part_number ?? '') + (updated.item_name ? ' — ' + updated.item_name : '');
                                    tr.children[2].textContent = updated.supplier ?? '';
                                }
                                tr.children[3].textContent = updated.qty_received ?? '';
                                tr.children[4].textContent = updated.unit_price ? parseFloat(updated.unit_price).toFixed(2) : '';
                                const editBtn = tr.querySelector('.edit-receiving-btn'); if (editBtn) editBtn.dataset.receiving = JSON.stringify(updated);
                                const delBtn = tr.querySelector('.delete-receiving-btn'); if (delBtn) delBtn.dataset.receivingId = updated.id;
                            }
                        } catch (e) { /* ignore */ }

                        // refresh dashboard summary
                        try {
                            const sumResp = await fetch('{{ route('api.dashboard.summary') }}', { headers:{ 'Accept':'application/json' } });
                            if (sumResp.ok) {
                                const sum = await sumResp.json();
                                const tp = document.getElementById('card-total-products'); if (tp) tp.textContent = sum.total_products;
                                const tv = document.getElementById('card-total-stock'); if (tv) tv.textContent = new Intl.NumberFormat('en-PH', { style:'currency', currency:'PHP' }).format(sum.total_stock_value || 0);
                                const tl = document.getElementById('card-low-stock'); if (tl) tl.textContent = sum.low_stock_count;
                            }
                        } catch (e) { /* ignore */ }

                    } catch (err) {
                        console.error('Edit submit failed', err);
                        alert('Failed to update receiving');
                    }
                });
            }

            // ===== Receive form submit via AJAX =====
            const receiveForm = document.getElementById('receiveForm');
            if (receiveForm) {
                receiveForm.addEventListener('submit', async (e) => {
                    e.preventDefault();
                    const url = receiveForm.action;
                    const fd = new FormData(receiveForm);
                    try {
                        const resp = await fetch(url, {
                            method: 'POST',
                            body: fd,
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });

                        if (!resp.ok) {
                            const data = await resp.json().catch(()=>null);
                            const msg = data && data.errors ? Object.values(data.errors).flat().join('\n') : 'Failed to save receiving';
                            alert(msg);
                            return;
                        }

                        // parse saved receiving returned by controller
                        const saved = await resp.json().catch(()=>null);

                        // success: close modal
                        closeModal(receiveModal);

                        // prepend to Received Items table if present
                        try {
                            const tbody = document.getElementById('receivedItemsTableBody');
                            if (tbody && saved) {
                                    // remove 'no items' placeholder row if present
                                    const firstRow = tbody.querySelector('tr');
                                    if (firstRow && firstRow.querySelector('td') && firstRow.querySelector('td').getAttribute('colspan') == '6') {
                                    tbody.innerHTML = '';
                                }

                                const tr = document.createElement('tr');
                                    tr.className = 'border-t';
                                    tr.dataset.receivingId = saved.id;

                                const tdDate = document.createElement('td'); tdDate.className='px-2 py-2'; tdDate.textContent = saved.date_received || (new Date()).toISOString().slice(0,10);
                                const tdPart = document.createElement('td'); tdPart.className='px-2 py-2'; tdPart.textContent = (saved.product && saved.product.part_number ? saved.product.part_number : '') + (saved.product && saved.product.name ? ' — ' + saved.product.name : '');
                                const tdSup = document.createElement('td'); tdSup.className='px-2 py-2'; tdSup.textContent = saved.product && saved.product.supplier ? saved.product.supplier : '';
                                const tdQty = document.createElement('td'); tdQty.className='px-2 py-2 text-right'; tdQty.textContent = saved.qty_received ?? '';
                                const tdPrice = document.createElement('td'); tdPrice.className='px-2 py-2 text-right'; tdPrice.textContent = saved.unit_price ? parseFloat(saved.unit_price).toFixed(2) : '';
                                    const tdAct = document.createElement('td'); tdAct.className='px-2 py-2 text-center';
                                    const editBtn = document.createElement('button'); editBtn.type='button'; editBtn.className='px-3 py-1 border rounded edit-receiving-btn'; editBtn.textContent='Edit'; editBtn.dataset.receiving = JSON.stringify(saved);
                                    const delBtn = document.createElement('button'); delBtn.type='button'; delBtn.className='px-3 py-1 border rounded ml-2 delete-receiving-btn'; delBtn.textContent='Delete'; delBtn.dataset.receivingId = saved.id;
                                    tdAct.appendChild(editBtn);
                                    tdAct.appendChild(delBtn);

                                    tr.appendChild(tdDate); tr.appendChild(tdPart); tr.appendChild(tdSup); tr.appendChild(tdQty); tr.appendChild(tdPrice); tr.appendChild(tdAct);

                                if (tbody.firstChild) tbody.insertBefore(tr, tbody.firstChild);
                                else tbody.appendChild(tr);

                                if (typeof window._attachEditHandlers === 'function') window._attachEditHandlers();
                                if (typeof window._attachDeleteHandlers === 'function') window._attachDeleteHandlers();

                                // remove product from top product list (prevent double-receive)
                                try {
                                    if (saved && saved.product && saved.product.id) {
                                        const prodTr = document.querySelector('[data-product-id="'+saved.product.id+'"]');
                                        if (prodTr) prodTr.remove();
                                    }
                                } catch(e) { /* ignore */ }
                            }
                        } catch (e) { /* ignore DOM errors */ }

                        // refresh product rows (will reattach receive handlers)
                        try { filterReceivingProducts(); } catch(e) { /* ignore */ }

                        // refresh dashboard cards if present
                        try {
                            const sumResp = await fetch('{{ route('api.dashboard.summary') }}', { headers:{ 'Accept':'application/json' } });
                            if (sumResp.ok) {
                                const sum = await sumResp.json();
                                const tp = document.getElementById('card-total-products'); if (tp) tp.textContent = sum.total_products;
                                const tv = document.getElementById('card-total-stock'); if (tv) tv.textContent = new Intl.NumberFormat('en-PH', { style:'currency', currency:'PHP' }).format(sum.total_stock_value || 0);
                                const tl = document.getElementById('card-low-stock'); if (tl) tl.textContent = sum.low_stock_count;
                            }
                        } catch (e) { /* ignore */ }

                    } catch (err) {
                        console.error('Receive submit failed', err);
                        alert('Failed to save receiving');
                    }
                });
            }

            // ===== Export Receiving Report =====
            const exportBtn = document.getElementById('exportReceivingBtn');
            if (exportBtn) {
                exportBtn.addEventListener('click', () => {
                    window.location.href = '{{ route('receiving.export') }}';
                });
            }

            // ===== Global ESC key closes any open modal =====
            document.addEventListener('keydown', e => {
                if (e.key !== 'Escape') return;
                [manualModal, addModal, receiveModal, editModal].forEach(m => {
                    if (m && !m.classList.contains('hidden')) {
                        closeModal(m);
                    }
                });
            });
        });
    </script>
@endsection
