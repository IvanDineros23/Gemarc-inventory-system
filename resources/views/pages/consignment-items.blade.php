@extends('layouts.app')

@section('title', 'Consignment Items | Gemarc LAN Based Inventory System')

@section('content')
    <div class="py-6">
        <div class="max-w-full mx-auto sm:px-2 lg:px-0">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @php $products = $products ?? collect(); @endphp

                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-semibold">Consignment Items</h2>
                        <div class="flex items-center space-x-2">
                            <button id="openAddConsignmentBtn" class="inline-block bg-green-600 text-white px-3 py-2 rounded hover:bg-green-700">Add new item</button>
                        </div>
                    </div>

                    <div class="mb-4">
                        <input type="text" id="consignmentSearch"
                               class="border rounded px-3 py-2 w-full max-w-md"
                               placeholder="Search products..."
                               oninput="filterConsignmentProducts()">
                    </div>

                    <div class="overflow-x-visible">
                        <table class="w-full text-sm border border-gray-300" id="consignmentTable">
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
                            <tbody id="consignmentTableBody">
                                @forelse($products as $product)
                                    <tr class="border-b hover:bg-gray-50" data-product-id="{{ $product->id }}">
                                        <td class="px-2 py-2 truncate text-center">{{ $product->part_number }}</td>
                                        <td class="px-2 py-2 truncate text-center">{{ $product->inventory_id }}</td>
                                        <td class="px-2 py-2 whitespace-normal break-words text-center">{{ $product->name }}</td>
                                        <td class="px-2 py-2 truncate text-center">{{ $product->supplier }}</td>
                                        <td class="px-2 py-2 text-center">{{ $product->ending_inventory ?? 0 }}</td>
                                        <td class="px-2 py-2 text-center whitespace-nowrap">
                                            <div class="inline-flex items-center gap-2">
                                                <button type="button" class="px-3 py-1 border rounded receive-btn" data-product='@json($product)'>Receive</button>
                                                <button type="button" class="px-3 py-1 border rounded delete-product-btn" data-product-id="{{ $product->id }}" data-product-name="{{ $product->name }}">Delete</button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-gray-500 py-4">No consignment items found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function filterConsignmentProducts() {
            const q = document.getElementById('consignmentSearch').value.toLowerCase();
            const rows = document.querySelectorAll('#consignmentTableBody tr[data-product-id]');
            rows.forEach(r => {
                const text = r.textContent.toLowerCase();
                r.style.display = text.includes(q) ? '' : 'none';
            });
        }
    </script>
    <script>
        // Wire delete buttons on consignment page
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.delete-product-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const id = this.getAttribute('data-product-id');
                    if (!confirm('Delete this product?')) return;
                    const fd = new FormData();
                    fd.append('_method', 'DELETE');
                    fd.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
                    fetch(`/product-management/${id}`, {
                        method: 'POST',
                        body: fd,
                    }).then(async res => {
                        if (!res.ok) {
                            alert('Delete failed');
                            return;
                        }
                        // remove row
                        const row = btn.closest('tr');
                        if (row) row.remove();
                    }).catch(err => {
                        console.error('Delete error', err);
                        alert('Delete failed');
                    });
                });
            });
        });
    </script>
    <!-- Add Consignment Modal -->
    <div id="addConsignmentModal" class="fixed inset-0 bg-black bg-opacity-40 hidden z-50">
        <div class="min-h-screen flex items-start md:items-center justify-center p-4">
            <div class="bg-white rounded shadow-lg w-11/12 md:w-3/4 lg:w-2/3 p-6 relative max-h-[90vh] overflow-y-auto">
                <button id="closeAddConsignmentModal" class="absolute top-3 right-3 text-gray-600">✕</button>
                <h3 class="text-lg font-semibold mb-4">Add Consignment Product</h3>

                <form id="addConsignmentForm" method="POST" action="{{ route('product.management.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="redirect_to" value="consignment">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                        <div>
                            <label class="block font-semibold">Part Number</label>
                            <input type="text" name="part_number" class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block font-semibold">Inventory ID</label>
                            <input type="text" name="inventory_id" class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block font-semibold">Brand</label>
                            <input type="text" name="brand" class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block font-semibold">Name</label>
                            <input type="text" name="name" class="w-full border rounded px-3 py-2" required>
                        </div>
                        <div>
                            <label class="block font-semibold">Description</label>
                            <textarea name="description" class="w-full border rounded px-3 py-2"></textarea>
                        </div>
                        <div>
                            <label class="block font-semibold">Supplier</label>
                            <input type="text" name="supplier" class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block font-semibold">F.O #</label>
                            <input type="text" name="fo_number" class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block font-semibold">Date Received</label>
                            <input type="date" name="date_received" class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block font-semibold">Qty. Received</label>
                            <input type="number" name="qty_received" class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block font-semibold">Unit Price</label>
                            <input type="number" step="0.01" name="unit_price" class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block font-semibold">Beginning Inventory</label>
                            <input type="number" name="beginning_inventory" class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block font-semibold">Ending Inventory</label>
                            <input type="number" name="ending_inventory" class="w-full border rounded px-3 py-2">
                        </div>
                        <div>
                            <label class="block font-semibold">TOTAL</label>
                            <input type="number" step="0.01" name="total" class="w-full border rounded px-3 py-2">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="block font-semibold">Product Image (optional)</label>
                        <input type="file" name="image" id="add_image" class="w-full">
                        <div id="add_image_preview" class="mt-3"></div>
                    </div>

                    <div class="mt-4">
                        <label class="inline-flex items-center gap-2">
                            <input type="checkbox" name="is_consignment" value="1" checked class="form-checkbox">
                            <span class="text-sm">Is consignment item</span>
                        </label>
                    </div>

                    <div class="mt-4 flex justify-end gap-2">
                        <button type="button" id="cancelAddConsignment" class="px-4 py-2 border rounded">Cancel</button>
                        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Modal open/close handlers
        const addConsignmentModal = document.getElementById('addConsignmentModal');
        const openAddConsignmentBtn = document.getElementById('openAddConsignmentBtn');
        const closeAddConsignmentModal = document.getElementById('closeAddConsignmentModal');
        const cancelAddConsignment = document.getElementById('cancelAddConsignment');

        if (openAddConsignmentBtn) {
            openAddConsignmentBtn.addEventListener('click', () => {
                document.body.style.overflow = 'hidden';
                addConsignmentModal.classList.remove('hidden');
            });
        }

        function closeAddModal() {
            addConsignmentModal.classList.add('hidden');
            document.body.style.overflow = '';
            document.getElementById('addConsignmentForm').reset();
            document.getElementById('add_image_preview').innerHTML = '';
        }

        if (closeAddConsignmentModal) closeAddConsignmentModal.addEventListener('click', closeAddModal);
        if (cancelAddConsignment) cancelAddConsignment.addEventListener('click', closeAddModal);

        if (addConsignmentModal) {
            addConsignmentModal.addEventListener('click', function (e) {
                if (e.target === addConsignmentModal) closeAddModal();
            });
        }

        // Close modal on ESC key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                try {
                    if (addConsignmentModal && !addConsignmentModal.classList.contains('hidden')) {
                        closeAddModal();
                    }
                } catch (err) {
                    // noop
                }
            }
        });

        // image preview
        const addImageInput = document.getElementById('add_image');
        if (addImageInput) {
            addImageInput.addEventListener('change', function () {
                const preview = document.getElementById('add_image_preview');
                preview.innerHTML = '';
                const file = this.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = function (ev) {
                    const img = document.createElement('img');
                    img.src = ev.target.result;
                    img.className = 'h-24';
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        }
    </script>
@endsection
