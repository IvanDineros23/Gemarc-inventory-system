@extends('layouts.app')

@section('title', 'Product Management | Gemarc LAN Based Inventory System')

@section('head')
    <!-- Add custom head content here if needed -->
@endsection

@section('content')
<div id="pageContent" class="bg-white p-2 rounded shadow max-w-full mx-auto">
    {{-- Product Form --}}
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded">
            <strong>There were some problems with your input:</strong>
            <ul class="mt-2 list-disc list-inside text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 border border-green-200 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif
    {{-- placeholder: Add button moved below to Product List header --}}

    <!-- Add Product Modal -->
    <div id="addModal" style="z-index:9999;" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center">
        <div class="bg-white rounded shadow-lg w-11/12 md:w-3/4 lg:w-2/3 p-6 relative max-h-[90vh] overflow-y-auto">
            <button id="closeAddModal" class="absolute top-3 right-3 text-gray-600">✕</button>
            <h3 class="text-lg font-semibold mb-4">Add Product</h3>

            <form id="addForm" method="POST" action="{{ route('product.management.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block font-semibold">Part Number</label>
                        <input type="text" name="part_number" id="add_part_number" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-semibold">Inventory ID</label>
                        <input type="text" name="inventory_id" id="add_inventory_id" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-semibold">Brand</label>
                        <input type="text" name="brand" id="add_brand" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-semibold">Name</label>
                        <input type="text" name="name" id="add_name" class="w-full border rounded px-3 py-2" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block font-semibold">Description</label>
                        <textarea name="description" id="add_description" class="w-full border rounded px-3 py-2"></textarea>
                    </div>
                    <div>
                        <label class="block font-semibold">Supplier</label>
                        <input type="text" name="supplier" id="add_supplier" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-semibold">F.O #</label>
                        <input type="text" name="fo_number" id="add_fo_number" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-semibold">Date Received</label>
                        <input type="date" name="date_received" id="add_date_received" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-semibold">Qty. Received</label>
                        <input type="number" name="qty_received" id="add_qty_received"
                               class="w-full border rounded px-3 py-2"
                               oninput="calculateTotal('add')">
                    </div>
                    <div>
                        <label class="block font-semibold">Unit Price</label>
                        <input type="number" step="0.01" name="unit_price" id="add_unit_price"
                               class="w-full border rounded px-3 py-2"
                               oninput="calculateTotal('add')">
                    </div>
                    <div>
                        <label class="block font-semibold">Beginning Inventory</label>
                        <input type="number" name="beginning_inventory" id="add_beginning_inventory" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-semibold">Ending Inventory</label>
                        <input type="number" name="ending_inventory" id="add_ending_inventory" class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block font-semibold">TOTAL</label>
                        <input type="number" step="0.01" name="total" id="add_total"
                               class="w-full border rounded px-3 py-2">
                    </div>
                </div>

                <div class="mt-4">
                    <label class="block font-semibold">Product Image (optional)</label>
                    <input type="file" name="image" id="add_image" class="w-full">
                </div>

                <div class="mt-4">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="is_consignment" id="add_is_consignment" value="1" class="form-checkbox">
                        <span class="text-sm">Is consignment item</span>
                    </label>
                </div>

                <div class="mt-4 flex gap-3">
                    <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Save Product</button>
                    <button type="button" id="cancelAdd" class="px-4 py-2 border rounded">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Product List Table --}}
    <div class="mt-10">
        <h3 class="text-xl font-semibold mb-2">Product List</h3>

        <div class="mb-4 flex justify-between items-center">
            <div>
                <button id="openAddModal" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">Add Product</button>
            </div>
            <div class="ml-4 flex-1 text-right">
                <input type="text" id="productSearch"
                       class="border rounded px-3 py-2 w-full max-w-xs inline-block"
                       placeholder="Search products..."
                       oninput="filterProducts()">
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full border text-xs md:text-sm" id="productTable">
                <thead class="bg-gray-700 text-white">
                    <tr>
                        <th class="px-2 py-2">Part Number</th>
                        <th class="px-2 py-2">Inventory ID</th>
                        <th class="px-2 py-2">Name</th>
                        <th class="px-2 py-2">Description</th>
                        <th class="px-2 py-2">Supplier</th>
                        <th class="px-2 py-2">F.O #</th>
                        <th class="px-2 py-2">Date Received</th>
                        <th class="px-2 py-2">Qty. Received</th>
                        <th class="px-2 py-2">Unit Price</th>
                        <th class="px-2 py-2">Beginning Inventory</th>
                        <th class="px-2 py-2">Ending Inventory</th>
                        <th class="px-2 py-2">TOTAL</th>
                        <th class="px-2 py-2">Image</th>
                        <th class="px-2 py-2">Action</th>
                    </tr>
                </thead>
                <tbody id="productTableBody">
                    @forelse($products as $product)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-2 py-2">{{ $product->part_number }}</td>
                            <td class="px-2 py-2">{{ $product->inventory_id }}</td>
                            <td class="px-2 py-2">{{ $product->name }}</td>
                            <td class="px-2 py-2">{{ $product->description }}</td>
                            <td class="px-2 py-2">{{ $product->supplier }}</td>
                            <td class="px-2 py-2">{{ $product->fo_number }}</td>
                            <td class="px-2 py-2">{{ $product->date_received }}</td>
                            <td class="px-2 py-2">{{ $product->qty_received }}</td>
                            <td class="px-2 py-2">{{ $product->unit_price }}</td>
                            <td class="px-2 py-2">{{ $product->beginning_inventory }}</td>
                            <td class="px-2 py-2">{{ $product->ending_inventory }}</td>
                            <td class="px-2 py-2">{{ $product->total }}</td>
                            <td class="px-2 py-2">
                                @if($product->image_path)
                                    <img src="{{ asset('storage/' . $product->image_path) }}" alt="Product Image" class="h-10 w-10 object-cover rounded">
                                @endif
                            </td>
                            <td class="px-2 py-2">
                                <div class="flex flex-col items-start space-y-2">
                                    <button type="button" class="w-full md:w-28 inline-block bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700 edit-btn" data-product='@json($product)'>Edit</button>
                                    <button type="button" class="w-full md:w-28 inline-block bg-red-600 text-white px-3 py-1 rounded text-xs hover:bg-red-700 delete-btn" data-id="{{ $product->id }}">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="14" class="text-center text-gray-500 py-4">No products found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
    <div class="bg-white rounded shadow-lg w-11/12 md:w-11/12 lg:w-4/5 xl:w-11/12 p-6 relative max-h-[90vh] overflow-y-auto">
        <button id="closeEditModal" class="absolute top-3 right-3 text-gray-600">✕</button>
        <h3 class="text-lg font-semibold mb-4">Edit Product</h3>

        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold">Part Number</label>
                    <input type="text" name="part_number" id="edit_part_number" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold">Inventory ID</label>
                    <input type="text" name="inventory_id" id="edit_inventory_id" class="w-full border rounded px-3 py-2">
                </div>
                <div class="md:col-span-2">
                    <label class="block font-semibold">Name</label>
                    <input type="text" name="name" id="edit_name" class="w-full border rounded px-3 py-2" required>
                </div>
                    <div class="md:col-span-2">
                        <label class="block font-semibold">Brand</label>
                        <input type="text" name="brand" id="edit_brand" class="w-full border rounded px-3 py-2">
                    </div>
                <div class="md:col-span-2">
                    <label class="block font-semibold">Description</label>
                    <textarea name="description" id="edit_description" class="w-full border rounded px-3 py-2"></textarea>
                </div>
                <div>
                    <label class="block font-semibold">Supplier</label>
                    <input type="text" name="supplier" id="edit_supplier" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold">F.O #</label>
                    <input type="text" name="fo_number" id="edit_fo_number" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold">Date Received</label>
                    <input type="date" name="date_received" id="edit_date_received" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold">Qty. Received</label>
                    <input type="number" name="qty_received" id="edit_qty_received" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold">Unit Price</label>
                    <input type="number" step="0.01" name="unit_price" id="edit_unit_price" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold">Beginning Inventory</label>
                    <input type="number" name="beginning_inventory" id="edit_beginning_inventory" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold">Ending Inventory</label>
                    <input type="number" name="ending_inventory" id="edit_ending_inventory" class="w-full border rounded px-3 py-2">
                </div>
                <div class="md:col-span-2">
                    <label class="block font-semibold">TOTAL</label>
                    <input type="number" step="0.01" name="total" id="edit_total" class="w-full border rounded px-3 py-2">
                </div>
                <div class="md:col-span-2">
                    <label class="block font-semibold">Product Image (optional)</label>
                    <input type="file" name="image" id="edit_image" class="w-full">
                    <div id="edit_image_preview" class="mt-3"></div>
                </div>
                <div class="md:col-span-2">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="is_consignment" id="edit_is_consignment" value="1" class="form-checkbox">
                        <span class="text-sm">Is consignment item</span>
                    </label>
                </div>
            </div>

            <div class="mt-4 flex gap-3">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Save Changes</button>
                <button type="button" id="cancelEdit" class="px-4 py-2 border rounded">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Delete confirm modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">
    <div class="bg-white rounded shadow-lg w-96 p-6">
        <h4 class="text-lg font-semibold mb-4">Confirm Delete</h4>
        <p class="mb-4">Are you sure you want to delete this product? This action cannot be undone.</p>
        <div class="flex justify-end gap-3">
            <button id="cancelDelete" class="px-4 py-2 border rounded">Cancel</button>
            <button id="confirmDelete" class="bg-red-600 text-white px-4 py-2 rounded">Delete</button>
        </div>
    </div>
</div>

{{-- Inline script for this page --}}
<script>
    let totalManualEdit = false;

    function calculateTotal(prefix = '') {
        if (totalManualEdit) return;
        const qtyEl = document.getElementById((prefix ? prefix + '_' : '') + 'qty_received') || { value: 0 };
        const priceEl = document.getElementById((prefix ? prefix + '_' : '') + 'unit_price') || { value: 0 };
        const totalEl = document.getElementById((prefix ? prefix + '_' : '') + 'total') || null;

        const qty = parseFloat(qtyEl.value) || 0;
        const price = parseFloat(priceEl.value) || 0;
        if (totalEl) totalEl.value = (qty * price).toFixed(2);
    }

    function filterProducts() {
        const input  = document.getElementById('productSearch');
        const filter = input.value.toLowerCase();
        const tbody  = document.getElementById('productTableBody');
        const rows   = tbody.querySelectorAll('tr');

        let matchCount = 0;

        rows.forEach(row => {
            const emptyRow = row.querySelector('td[colspan]');
            if (emptyRow) {
                // Ipakita lang yung "No products found" kung walang match
                row.style.display = matchCount === 0 ? '' : 'none';
            } else {
                const text = row.textContent.toLowerCase();
                if (text.indexOf(filter) > -1) {
                    row.style.display = '';
                    matchCount++;
                } else {
                    row.style.display = 'none';
                }
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        const totalInput = document.getElementById('total');
        const addTotalInput = document.getElementById('add_total');

        if (totalInput) {
            totalInput.addEventListener('focus', function () {
                totalManualEdit = true;
            });

            totalInput.addEventListener('blur', function () {
                totalManualEdit = false;
                calculateTotal();
            });
        }

        if (addTotalInput) {
            addTotalInput.addEventListener('focus', function () { totalManualEdit = true; });
            addTotalInput.addEventListener('blur', function () { totalManualEdit = false; calculateTotal('add'); });
        }

        calculateTotal();
        calculateTotal('add');

        // Add modal open/close handlers
        const openAdd = document.getElementById('openAddModal');
        const addModal = document.getElementById('addModal');
        const closeAdd = document.getElementById('closeAddModal');
        const cancelAdd = document.getElementById('cancelAdd');

        // helper to find focusable elements
        function getFocusableElements(container){
            return Array.from(container.querySelectorAll('a[href], button:not([disabled]), textarea, input, select, [tabindex]:not([tabindex="-1"])'))
                .filter(el => el.offsetParent !== null);
        }

        let _lastActiveElAdd = null;
        let _addOutsideHandler = null;
        function showAddModal(){
            _lastActiveElAdd = document.activeElement;
            document.body.style.overflow = 'hidden';
            // disable background interactions
            const page = document.getElementById('pageContent');
            if (page) { page.style.pointerEvents = 'none'; page.setAttribute('aria-hidden','true'); }
            addModal.classList.remove('hidden');
            addModal.classList.add('flex');
            // focus first focusable
            const dialog = addModal.querySelector('.bg-white');
            const focusables = getFocusableElements(dialog);
            if (focusables.length) focusables[0].focus();

            // attach document-level mousedown handler to reliably catch outside clicks
            _addOutsideHandler = function(ev){
                try{
                    if (!dialog.contains(ev.target)) {
                        hideAddModal();
                    }
                }catch(e){}
            };
            document.addEventListener('mousedown', _addOutsideHandler);
        }

        function hideAddModal(){
            addModal.classList.remove('flex');
            addModal.classList.add('hidden');
            const page = document.getElementById('pageContent');
            if (page) { page.style.pointerEvents = ''; page.removeAttribute('aria-hidden'); }
            document.body.style.overflow = '';
            try{ if (_lastActiveElAdd && typeof _lastActiveElAdd.focus === 'function') _lastActiveElAdd.focus(); }catch(e){}
            if (_addOutsideHandler) { document.removeEventListener('mousedown', _addOutsideHandler); _addOutsideHandler = null; }
        }

        // expose to global so other handlers (global ESC) can use the same cleanup
        window.hideAddModal = hideAddModal;
        window.showAddModal = showAddModal;

        if (openAdd && addModal) {
            openAdd.addEventListener('click', () => showAddModal());
        }

        if (closeAdd) closeAdd.addEventListener('click', hideAddModal);
        if (cancelAdd) cancelAdd.addEventListener('click', hideAddModal);

        // Close add modal when clicking outside (backdrop)
        if (addModal) {
            addModal.addEventListener('click', function (e) {
                const dialog = addModal.querySelector('.bg-white');
                // if click happened outside the dialog element, close
                if (dialog && !dialog.contains(e.target)) {
                    hideAddModal();
                }
            });
            // prevent clicks inside dialog from bubbling to overlay
            const dialogEl = addModal.querySelector('.bg-white');
            if (dialogEl) {
                dialogEl.addEventListener('click', function(ev){ ev.stopPropagation(); });
            }
        }

        // Keyboard handling (Escape to close, Tab to trap focus inside addModal)
        document.addEventListener('keydown', (ev) => {
            if (addModal.classList.contains('hidden')) return;
            if (ev.key === 'Escape' || ev.key === 'Esc') { hideAddModal(); return; }
            if (ev.key === 'Tab'){
                const dialog = addModal.querySelector('.bg-white');
                const focusables = getFocusableElements(dialog);
                if (!focusables.length) return;
                const first = focusables[0];
                const last = focusables[focusables.length - 1];
                if (ev.shiftKey){
                    if (document.activeElement === first){ ev.preventDefault(); last.focus(); }
                } else {
                    if (document.activeElement === last){ ev.preventDefault(); first.focus(); }
                }
            }
        });

        // Preview image on select in add modal
        const addImageInput = document.getElementById('add_image');
        if (addImageInput) {
            addImageInput.addEventListener('change', function () {
                // preview not required for now; could add preview area similar to edit modal
            });
        }
    });

    // Edit modal logic
    const editModal = document.getElementById('editModal');
    const editForm = document.getElementById('editForm');
    const closeEditModalBtn = document.getElementById('closeEditModal');
    const cancelEditBtn = document.getElementById('cancelEdit');

    function openEditModal(product) {
        // populate fields
        document.getElementById('edit_part_number').value = product.part_number || '';
        document.getElementById('edit_inventory_id').value = product.inventory_id || '';
        document.getElementById('edit_brand').value = product.brand || '';
        document.getElementById('edit_name').value = product.name || '';
        document.getElementById('edit_description').value = product.description || '';
        document.getElementById('edit_supplier').value = product.supplier || '';
        document.getElementById('edit_fo_number').value = product.fo_number || '';
        document.getElementById('edit_date_received').value = product.date_received || '';
        document.getElementById('edit_qty_received').value = product.qty_received || '';
        document.getElementById('edit_unit_price').value = product.unit_price || '';
        document.getElementById('edit_beginning_inventory').value = product.beginning_inventory || '';
        document.getElementById('edit_ending_inventory').value = product.ending_inventory || '';
        document.getElementById('edit_total').value = product.total || '';
        document.getElementById('edit_is_consignment').checked = product.is_consignment ? true : false;

        // set form action to update route
        editForm.action = '/product-management/' + product.id;

        // set image preview
        const preview = document.getElementById('edit_image_preview');
        preview.innerHTML = '';
        if (product.image_path) {
            const img = document.createElement('img');
            img.src = '/storage/' + product.image_path;
            img.className = 'h-24';
            preview.appendChild(img);
        }

            // show modal
            // prevent background scroll and focus modal
            document.body.style.overflow = 'hidden';
            editModal.classList.remove('hidden');
            editModal.classList.add('flex');
            // focus first input
            setTimeout(() => {
                const firstInput = document.getElementById('edit_name');
                if (firstInput) firstInput.focus();
            }, 50);
    }

    function closeEditModal() {
        editModal.classList.remove('flex');
        editModal.classList.add('hidden');
        editForm.reset();
        document.getElementById('edit_image_preview').innerHTML = '';
        // restore body scrolling
        document.body.style.overflow = '';
        clearValidationErrors();
    }

    // Close modal when clicking outside content
    if (editModal) {
        editModal.addEventListener('click', function (e) {
            if (e.target === editModal) {
                closeEditModal();
            }
        });
    }

    // Close on ESC key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            // if add modal open, use centralized hide helper
            const addModalEl = document.getElementById('addModal');
            if (addModalEl && !addModalEl.classList.contains('hidden')) {
                if (typeof window.hideAddModal === 'function') { window.hideAddModal(); return; }
            }

            if (!editModal.classList.contains('hidden')) {
                closeEditModal();
                return;
            }

            const deleteModalEl = document.getElementById('deleteModal');
            if (deleteModalEl && !deleteModalEl.classList.contains('hidden')) {
                // use hideDeleteModal to ensure cleanup (restore scrolling/pointer events)
                if (typeof hideDeleteModal === 'function') hideDeleteModal();
            }
        }
    });

    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const product = JSON.parse(btn.getAttribute('data-product'));
            openEditModal(product);
        });
    });

    if (closeEditModalBtn) closeEditModalBtn.addEventListener('click', closeEditModal);
    if (cancelEditBtn) cancelEditBtn.addEventListener('click', closeEditModal);

    // preview image on select in edit modal
    const editImageInput = document.getElementById('edit_image');
    if (editImageInput) {
        editImageInput.addEventListener('change', function () {
            const preview = document.getElementById('edit_image_preview');
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

    // Helper to show temporary AJAX messages
    function showMessage(message, type = 'success') {
        let container = document.getElementById('ajaxMessage');
        if (!container) {
            container = document.createElement('div');
            container.id = 'ajaxMessage';
            container.className = 'fixed top-4 right-4 z-50';
            document.body.appendChild(container);
        }

        const msg = document.createElement('div');
        msg.className = (type === 'success') ? 'mb-2 p-3 bg-green-600 text-white rounded shadow' : 'mb-2 p-3 bg-red-600 text-white rounded shadow';
        msg.textContent = message;
        container.appendChild(msg);

        setTimeout(() => {
            msg.remove();
            if (!container.hasChildNodes()) container.remove();
        }, 3500);
    }

    // Validation display helpers
    function clearValidationErrors() {
        document.querySelectorAll('.validation-error').forEach(el => el.remove());
    }

    function showValidationErrors(errors) {
        clearValidationErrors();
        Object.keys(errors).forEach(field => {
            const input = editForm.querySelector(`[name="${field}"]`);
            const msgs = errors[field];
            if (input) {
                const wrap = document.createElement('div');
                wrap.className = 'validation-error mt-1 text-sm text-red-600';
                wrap.textContent = msgs.join(' ');
                input.parentNode.insertBefore(wrap, input.nextSibling);
            }
        });
    }

    // AJAX submit for edit form
    if (editForm) {
        editForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const csrfMeta = document.querySelector('meta[name="csrf-token"]');
            const csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : document.querySelector('input[name="_token"]').value;

            const formData = new FormData(editForm);

            // send as POST with _method=PUT (FormData contains _method from blade)
            fetch(editForm.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            }).then(async res => {
                if (!res.ok) {
                    if (res.status === 422) {
                        const data = await res.json().catch(() => null);
                        if (data && data.errors) {
                            showValidationErrors(data.errors);
                            return;
                        }
                    }
                    const txt = await res.text();
                    showMessage('Update failed', 'error');
                    console.error('Update error', res.status, txt);
                    return;
                }

                const data = await res.json();

                // Update table row for this product
                const id = data.id;
                const deleteBtn = document.querySelector(`button.delete-btn[data-id="${id}"]`);
                if (deleteBtn) {
                    const row = deleteBtn.closest('tr');
                    if (row) {
                        const cells = row.querySelectorAll('td');
                        if (cells.length >= 13) {
                            cells[0].textContent = data.part_number || '';
                            cells[1].textContent = data.inventory_id || '';
                            cells[2].textContent = data.name || '';
                            cells[3].textContent = data.description || '';
                            cells[4].textContent = data.supplier || '';
                            cells[5].textContent = data.fo_number || '';
                            cells[6].textContent = data.date_received || '';
                            cells[7].textContent = data.qty_received ?? '';
                            cells[8].textContent = data.unit_price ?? '';
                            cells[9].textContent = data.beginning_inventory ?? '';
                            cells[10].textContent = data.ending_inventory ?? '';
                            cells[11].textContent = data.total ?? '';
                            // image cell
                            if (data.image_path) {
                                cells[12].innerHTML = `<img src="/storage/${data.image_path}" alt="Product Image" class="h-10 w-10 object-cover rounded">`;
                            } else {
                                cells[12].innerHTML = '';
                            }

                            // update data-product on edit button
                            const editBtn = row.querySelector('.edit-btn');
                            if (editBtn) {
                                editBtn.setAttribute('data-product', JSON.stringify(data));
                            }
                        }
                    }
                }

                showMessage('Product updated successfully');
                closeEditModal();
            }).catch(err => {
                console.error('Update fetch error', err);
                showMessage('Update failed', 'error');
            });
        });
    }

    // Delete modal logic
    const deleteModal = document.getElementById('deleteModal');
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    const cancelDeleteBtn = document.getElementById('cancelDelete');
    let deleteTargetId = null;

    function hideDeleteModal(){
        deleteTargetId = null;
        if (deleteModal) {
            deleteModal.classList.remove('flex');
            deleteModal.classList.add('hidden');
        }
        // restore body scrolling
        document.body.style.overflow = '';
        // ensure page content is interactive again
        const page = document.getElementById('pageContent');
        if (page) { page.style.pointerEvents = ''; page.removeAttribute('aria-hidden'); }
    }

    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            deleteTargetId = btn.getAttribute('data-id');
            // prevent background scroll
            document.body.style.overflow = 'hidden';
            deleteModal.classList.remove('hidden');
            deleteModal.classList.add('flex');
        });
    });

    if (cancelDeleteBtn) {
        cancelDeleteBtn.addEventListener('click', () => {
            deleteTargetId = null;
            hideDeleteModal();
        });
    }

    // Close delete modal when clicking outside
    if (deleteModal) {
        deleteModal.addEventListener('click', function (e) {
            if (e.target === deleteModal) {
                hideDeleteModal();
            }
        });
    }

    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', () => {
            if (!deleteTargetId) return;

            const token = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : document.querySelector('input[name="_token"]').value;

            const fd = new FormData();
            fd.append('_method', 'DELETE');
            fd.append('_token', token);

            fetch(`/product-management/${deleteTargetId}`, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: fd
            }).then(async res => {
                if (!res.ok) {
                    const txt = await res.text();
                    console.error('Delete failed', res.status, txt);
                    showMessage('Delete failed', 'error');
                    document.body.style.overflow = '';
                    return;
                }

                const data = await res.json();
                // remove row
                const delBtn = document.querySelector(`button.delete-btn[data-id="${deleteTargetId}"]`);
                if (delBtn) {
                    const row = delBtn.closest('tr');
                    if (row) row.remove();
                }

                showMessage('Product deleted');
                hideDeleteModal();
            }).catch(err => {
                console.error('Delete fetch error', err);
                showMessage('Delete failed', 'error');
                document.body.style.overflow = '';
            });
        });
    }
</script>
@endsection
