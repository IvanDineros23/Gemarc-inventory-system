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

                    <form method="POST" action="{{ route('delivery.store') }}" class="space-y-4" id="delivery-form">
                        @csrf

                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm text-gray-700">Customer</label>
                                <input type="text" name="customer" value="{{ old('customer') }}" class="block w-full mt-1 rounded border-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700">DR Number</label>
                                <input type="text" name="dr_number" value="{{ old('dr_number') }}" class="block w-full mt-1 rounded border-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700">DR Date</label>
                                <input type="datetime-local" name="dr_date" value="{{ old('dr_date') }}" class="block w-full mt-1 rounded border-gray-300">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-700">Intended to</label>
                            <div id="intended-list" class="mt-2 space-y-2">
                                @php
                                    $oldIntended = old('intended_to');
                                    $intendedItems = [];
                                    if ($oldIntended) {
                                        // if old value looks like JSON array, decode it; otherwise split by newline
                                        $decoded = json_decode($oldIntended, true);
                                        if (is_array($decoded)) {
                                            $intendedItems = $decoded;
                                        } else {
                                            $intendedItems = array_filter(array_map('trim', explode("\n", $oldIntended)));
                                        }
                                    }
                                @endphp

                                @if(count($intendedItems) > 0)
                                    @foreach($intendedItems as $itm)
                                        <div class="flex gap-2">
                                            <input type="text" class="intended-input block w-full mt-1 rounded border-gray-300" value="{{ $itm }}">
                                            <button type="button" class="remove-intended inline-block px-3 py-1 bg-red-600 text-white rounded">Remove</button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="flex gap-2">
                                        <input type="text" class="intended-input block w-full mt-1 rounded border-gray-300" value="">
                                        <button type="button" class="remove-intended inline-block px-3 py-1 bg-red-600 text-white rounded">Remove</button>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-2">
                                <button type="button" id="add-intended" class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-sm rounded">Add Intended To</button>
                            </div>

                            <input type="hidden" name="intended_to" id="intended-hidden" value="{{ old('intended_to') }}">
                        </div>

                        <hr class="my-4">

                        <h4 class="font-semibold">Delivery Cart Entry</h4>

                        <div class="grid grid-cols-4 gap-3 items-end">
                            <div>
                                <label class="block text-sm text-gray-700">Product</label>
                                <select id="cart-product" class="block w-full mt-1 rounded border-gray-300">
                                    <option value="">Choose product</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}" data-part="{{ $p->part_number }}" data-unitprice="{{ $p->unit_price }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700">Quantity</label>
                                <input type="number" id="cart-qty" min="1" value="1" class="block w-full mt-1 rounded border-gray-300">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-700">Unit Cost</label>
                                <input type="number" id="cart-unitcost" step="0.01" class="block w-full mt-1 rounded border-gray-300">
                            </div>
                            <div>
                                <button type="button" id="add-to-cart" class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm rounded">Add to cart</button>
                            </div>
                        </div>

                        <div class="mt-4">
                            <table class="min-w-full bg-white" id="cart-table">
                                <thead>
                                    <tr class="text-left text-xs text-gray-500 uppercase">
                                        <th class="px-3 py-2">Product</th>
                                        <th class="px-3 py-2">Part #</th>
                                        <th class="px-3 py-2">Qty</th>
                                        <th class="px-3 py-2">Unit Cost</th>
                                        <th class="px-3 py-2">Actions</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <input type="hidden" name="remarks" id="remarks-field" value="{{ old('remarks') }}">

                        <div class="pt-2">
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded">Post to RR Files (Record Delivery)</button>
                        </div>
                    </form>

                    <script>
                        (function(){
                            const addBtn = document.getElementById('add-to-cart');
                            const productSelect = document.getElementById('cart-product');
                            const qtyInput = document.getElementById('cart-qty');
                            const unitCostInput = document.getElementById('cart-unitcost');
                            const cartTableBody = document.querySelector('#cart-table tbody');
                            const form = document.getElementById('delivery-form');

                            function createHiddenInputsForItem(index, item){
                                const container = document.createElement('div');
                                container.dataset.index = index;
                                // create inputs named items[index][field]
                                for (const key in item){
                                    const input = document.createElement('input');
                                    input.type = 'hidden';
                                    input.name = `items[${index}][${key}]`;
                                    input.value = item[key];
                                    container.appendChild(input);
                                }
                                return container;
                            }

                            let cartIndex = 0;

                            addBtn.addEventListener('click', () => {
                                const pid = productSelect.value;
                                if (!pid) {
                                    showToast('Please choose a product', 'warning');
                                    return;
                                }
                                const pname = productSelect.options[productSelect.selectedIndex].text;
                                const part = productSelect.options[productSelect.selectedIndex].dataset.part || '';
                                const qty = parseInt(qtyInput.value) || 1;
                                const unitCost = unitCostInput.value || productSelect.options[productSelect.selectedIndex].dataset.unitprice || '';

                                const tr = document.createElement('tr');
                                tr.innerHTML = `<td class="px-3 py-2">${pname}</td><td class="px-3 py-2">${part}</td><td class="px-3 py-2">${qty}</td><td class="px-3 py-2">${unitCost}</td><td class="px-3 py-2"><button type=\"button\" class=\"remove-row inline-block px-2 py-1 bg-red-600 text-white rounded text-sm\">Remove</button></td>`;

                                // hidden inputs
                                const item = {
                                    product_id: pid,
                                    part_number: part,
                                    item_name: pname,
                                    qty: qty,
                                    unit_cost: unitCost,
                                };
                                const hidden = createHiddenInputsForItem(cartIndex, item);
                                tr.appendChild(hidden);

                                cartTableBody.appendChild(tr);

                                cartIndex++;
                            });

                            // remove handler
                            cartTableBody.addEventListener('click', (e) => {
                                if (e.target.classList.contains('remove-row')) {
                                    const tr = e.target.closest('tr');
                                    tr.remove();
                                }
                            });

                            // Intended-to dynamic list
                            const addIntendedBtn = document.getElementById('add-intended');
                            const intendedList = document.getElementById('intended-list');
                            const intendedHidden = document.getElementById('intended-hidden');

                            addIntendedBtn.addEventListener('click', () => {
                                const wrapper = document.createElement('div');
                                wrapper.className = 'flex gap-2';
                                const input = document.createElement('input');
                                input.type = 'text';
                                input.className = 'intended-input block w-full mt-1 rounded border-gray-300';
                                const removeBtn = document.createElement('button');
                                removeBtn.type = 'button';
                                removeBtn.className = 'remove-intended inline-block px-3 py-1 bg-red-600 text-white rounded';
                                removeBtn.textContent = 'Remove';
                                wrapper.appendChild(input);
                                wrapper.appendChild(removeBtn);
                                intendedList.appendChild(wrapper);
                            });

                            intendedList.addEventListener('click', (e) => {
                                if (e.target.classList.contains('remove-intended')) {
                                    const tr = e.target.closest('div');
                                    tr.remove();
                                }
                            });

                            // Before submit, collect intended items into hidden input as JSON
                            form.addEventListener('submit', (e) => {
                                const values = Array.from(document.querySelectorAll('.intended-input')).map(i => i.value.trim()).filter(Boolean);
                                intendedHidden.value = JSON.stringify(values);
                            });
                        })();
                    </script>

                </div>
            </div>
        </div>
    </div>
@endsection
