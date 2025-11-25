@extends('layouts.app')

@section('title', 'Inventory per Supplier | Gemarc LAN Based Inventory System')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" x-data='inventoryPage({!! json_encode($suppliers) !!}, {{ $grandTotal }})'>

                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <input x-model="query" type="search" placeholder="Search supplier, brand, product..." class="border rounded px-3 py-2 w-96" />
                            <button @click="clear()" class="px-3 py-2 bg-gray-100 rounded">Clear</button>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="text-sm text-gray-700">Grand Total: <strong class="ml-2">₱ <span x-text="formatMoney(grandTotal)"></span></strong></div>
                            <a :href="printUrl" target="_blank" class="px-3 py-2 bg-green-600 text-white rounded">Print Detailed Report</a>
                        </div>
                    </div>

                    <template x-if="suppliersList.length === 0">
                        <div class="text-gray-500">No suppliers or products found.</div>
                    </template>

                    <template x-for="supplier in filteredSuppliers()" :key="supplier.name">
                        <div class="mb-6 border rounded">
                            <div class="bg-gray-50 px-4 py-3 flex items-center justify-between">
                                <div>
                                    <h3 class="font-semibold" x-text="supplier.name"></h3>
                                    <div class="text-sm text-gray-600">Supplier Total: ₱ <span x-text="formatMoney(supplier.total)"></span></div>
                                </div>
                            </div>

                            <div class="p-4">
                                <template x-for="brand in supplier.brands" :key="brand.name">
                                    <div class="mb-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <div class="font-medium text-gray-700" x-text="brand.name"></div>
                                            <div class="text-sm text-gray-600">Brand Total: ₱ <span x-text="formatMoney(brand.total)"></span></div>
                                        </div>

                                        <div class="overflow-x-auto">
                                            <table class="min-w-full text-sm">
                                                <thead>
                                                    <tr class="text-left text-gray-600">
                                                        <th class="px-2 py-1">Part #</th>
                                                        <th class="px-2 py-1">Inventory ID</th>
                                                        <th class="px-2 py-1">Name</th>
                                                        <th class="px-2 py-1">Qty</th>
                                                        <th class="px-2 py-1">Unit</th>
                                                        <th class="px-2 py-1">Unit Price</th>
                                                        <th class="px-2 py-1">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <template x-for="prod in brand.products" :key="prod.id">
                                                        <tr class="border-t">
                                                            <td class="px-2 py-2" x-text="prod.part_number"></td>
                                                            <td class="px-2 py-2" x-text="prod.inventory_id"></td>
                                                            <td class="px-2 py-2" x-text="prod.name"></td>
                                                            <td class="px-2 py-2" x-text="prod.qty"></td>
                                                            <td class="px-2 py-2" x-text="prod.unit"></td>
                                                            <td class="px-2 py-2">₱ <span x-text="formatMoney(prod.unit_price)"></span></td>
                                                            <td class="px-2 py-2">₱ <span x-text="formatMoney(prod.total)"></span></td>
                                                        </tr>
                                                    </template>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>

                </div>
            </div>
        </div>
    </div>

    <script>
        function inventoryPage(initialSuppliers, grandTotal) {
            return {
                suppliersList: Object.values(initialSuppliers),
                query: '',
                grandTotal: parseFloat(grandTotal) || 0,
                printUrl: '{{ route('inventory.per.supplier.print') }}',
                clear() { this.query = '' },
                formatMoney(v) {
                    v = parseFloat(v) || 0;
                    return v.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                },
                filteredSuppliers() {
                    if (!this.query) return this.suppliersList;
                    const q = this.query.toLowerCase();
                    return this.suppliersList.map(s => {
                        const brands = s.brands.map(b => {
                            const prods = b.products.filter(p => (
                                (p.part_number||'').toString().toLowerCase().includes(q) ||
                                (p.inventory_id||'').toString().toLowerCase().includes(q) ||
                                (p.name||'').toString().toLowerCase().includes(q) ||
                                (b.name||'').toString().toLowerCase().includes(q)
                            ));
                            return { ...b, products: prods };
                        }).filter(b => b.products.length > 0 || (b.name||'').toLowerCase().includes(q));

                        const hasSupplierMatch = (s.name||'').toLowerCase().includes(q);
                        if (brands.length > 0 || hasSupplierMatch) {
                            return { ...s, brands };
                        }
                        return null;
                    }).filter(Boolean);
                }
            }
        }
    </script>
@endsection
