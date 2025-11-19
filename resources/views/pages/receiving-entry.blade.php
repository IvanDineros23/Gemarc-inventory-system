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
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
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

                    <div class="overflow-x-auto">
                        <table class="w-full table-fixed border text-sm" id="receivingTable">
                            <thead class="bg-gray-700 text-white">
                                <tr>
                                    <th class="px-2 py-2 w-1/6 text-center">Part Number</th>
                                    <th class="px-2 py-2 w-1/6 text-center">Inventory ID</th>
                                    <th class="px-2 py-2 w-2/6 text-center">Name</th>
                                    <th class="px-2 py-2 w-1/6 text-center">Supplier</th>
                                    <th class="px-2 py-2 w-1/12 text-center">Qty On Hand</th>
                                    <th class="px-2 py-2 w-1/12 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody id="receivingTableBody">
                                @forelse($products as $product)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-2 py-2 truncate text-center">{{ $product->part_number }}</td>
                                        <td class="px-2 py-2 truncate text-center">{{ $product->inventory_id }}</td>
                                        <td class="px-2 py-2 whitespace-normal break-words text-center">
                                            {{ $product->name }}
                                        </td>
                                        <td class="px-2 py-2 truncate text-center">{{ $product->supplier }}</td>
                                        <td class="px-2 py-2 text-center">
                                            {{ $product->ending_inventory ?? 0 }}
                                        </td>
                                        <td class="px-2 py-2 text-center">
                                            <button type="button"
                                                    class="inline-block bg-green-600 text-white px-3 py-1 rounded receive-btn"
                                                    data-product='@json($product)'>
                                                Receive
                                            </button>
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

                                <form id="receiveForm" method="POST" enctype="multipart/form-data">
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

                                <form id="manualReceivingForm" method="POST" enctype="multipart/form-data" class="w-full">
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
                            tdAct.className = 'px-2 py-2 text-center';
                            const btn = document.createElement('button');
                            btn.type = 'button';
                            btn.className = 'inline-block bg-indigo-600 text-white px-3 py-1 rounded receive-btn';
                            btn.textContent = 'Receive';
                            btn.dataset.product = JSON.stringify(p);
                            tdAct.appendChild(btn);

                            tr.appendChild(tdPart);
                            tr.appendChild(tdInv);
                            tr.appendChild(tdName);
                            tr.appendChild(tdSup);
                            tr.appendChild(tdQty);
                            tr.appendChild(tdAct);

                            tbody.appendChild(tr);
                        });

                        // reattach receive button handlers (defined during DOMContentLoaded)
                        if (typeof window._attachReceiveHandlers === 'function') {
                            window._attachReceiveHandlers();
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

            // ===== Global ESC key closes any open modal =====
            document.addEventListener('keydown', e => {
                if (e.key !== 'Escape') return;
                [manualModal, addModal, receiveModal].forEach(m => {
                    if (m && !m.classList.contains('hidden')) {
                        closeModal(m);
                    }
                });
            });
        });
    </script>
@endsection
