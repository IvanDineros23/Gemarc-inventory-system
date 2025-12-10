@extends('layouts.app')

@section('title', 'Dashboard | Gemarc LAN Based Inventory System')

@section('content')
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="flex items-center justify-between mb-4">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full mr-4">
                    <div class="bg-white p-4 rounded shadow flex items-center gap-3">
                        <div class="p-3 bg-blue-100 rounded flex items-center justify-center">
                            <img src="{{ asset('images/box.png') }}" alt="Total products" class="w-8 h-8 object-contain">
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Total Products</div>
                            <div id="card-total-products" class="text-2xl font-bold">—</div>
                        </div>
                    </div>
                    <div class="bg-white p-4 rounded shadow flex items-center gap-3">
                        <div class="p-3 bg-green-100 rounded flex items-center justify-center">
                            <img src="{{ asset('images/32724.png') }}" alt="Total stock value" class="w-8 h-8 object-contain">
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Total Stock Value</div>
                            <div id="card-total-stock" class="text-2xl font-bold">—</div>
                        </div>
                    </div>
                    <div class="bg-white p-4 rounded shadow flex items-center gap-3">
                        <div class="p-3 bg-yellow-100 rounded flex items-center justify-center">
                            <img src="{{ asset('images/trend.png') }}" alt="Low stock" class="w-8 h-8 object-contain">
                        </div>
                        <div>
                            <div class="text-sm text-gray-500">Low Stock Items</div>
                            <div id="card-low-stock" class="text-2xl font-bold">—</div>
                            <div id="card-low-stock-list" class="text-xs text-gray-600 mt-1 space-y-0.5" style="max-width:220px;">
                                {{-- small preview of items will be injected here (up to 3 items) --}}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- controls relocated into charts panel -->
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <div class="lg:col-span-2 bg-white p-4 rounded shadow relative">
                    <div class="absolute top-4 right-4 flex items-center gap-2">
                        <a href="{{ route('dashboard.export.lowstock', ['format' => 'pdf']) }}" target="_blank" class="inline-block bg-white border text-gray-700 px-3 py-2 rounded hover:bg-gray-50">Print Low Stock Items (PDF)</a>
                        <button id="refreshDashboard" class="inline-block bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700">Refresh</button>
                    </div>

                    <h3 class="font-semibold mb-2">Receivings (last 6 months)</h3>
                    <div class="h-56 md:h-64">
                        <canvas id="receivingsChart"></canvas>
                    </div>
                    <div class="mt-4">
                        <h4 class="font-semibold mb-2">Incoming Stock Value (last 6 months)</h4>
                        <div class="text-xs text-gray-500 mb-2">This chart shows incoming value by month (sum of qty_received × unit_price). The Total Stock Value card shows current stock value using ending inventory × unit price.</div>
                        <div class="h-44 md:h-48">
                            <canvas id="stockValueChart"></canvas>
                        </div>
                    </div>
                </div>

                <div class="bg-white p-4 rounded shadow h-full flex flex-col">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <h3 class="font-semibold">Low Stock Items</h3>
                            <a href="{{ route('product.management') }}" class="text-sm text-blue-600">View all</a>
                        </div>

                        <div id="lowStockList" class="space-y-2 text-sm">
                            <div class="text-gray-500">Loading…</div>
                        </div>

                        {{-- 🔹 QUICK LOW STOCK STATS --}}
                        <div class="mt-3 grid grid-cols-3 gap-2 text-xs">
                            <div class="p-2 bg-slate-50 rounded">
                                <div class="text-[10px] text-gray-500 uppercase">Low stock</div>
                                <div id="lowStockCountBadge" class="text-lg font-semibold">—</div>
                            </div>
                            <div class="col-span-2 p-2 bg-slate-50 rounded">
                                <div class="text-[10px] text-gray-500 uppercase">Most critical item</div>
                                <div id="criticalLowStockItem" class="font-semibold truncate">—</div>
                            </div>
                        </div>

                        <div class="mt-2 grid grid-cols-2 gap-2 text-xs">
                            <div class="p-2 bg-slate-50 rounded">
                                <div class="text-[10px] text-gray-500 uppercase">Avg on-hand</div>
                                <div id="avgLowStockOnHand" class="font-semibold">—</div>
                            </div>
                            <div class="p-2 bg-slate-50 rounded">
                                <div class="text-[10px] text-gray-500 uppercase">Top supplier</div>
                                <div id="topSupplierName" class="font-semibold truncate">—</div>
                                <div id="topSupplierValue" class="text-[11px] text-gray-500">—</div>
                            </div>
                        </div>
                    </div>

                    {{-- 🔹 TOP SUPPLIERS CHART FILLS REMAINING SPACE --}}
                    <div class="mt-4 flex-1 flex flex-col">
                        <h4 class="font-semibold mb-2">Top Suppliers</h4>
                        <div class="flex-1">
                            <canvas id="topSuppliersChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Prevent going back to login page
        (function() {
            // Replace history state to remove login page from history
            if (window.history && window.history.pushState) {
                window.history.pushState(null, null, window.location.href);
                window.onpopstate = function() {
                    window.history.pushState(null, null, window.location.href);
                };
            }
        })();

        let receivingsChartObj = null;
        let stockValueChartObj = null;
        let topSuppliersChartObj = null;

        async function fetchSummary() {
            const resp = await fetch("{{ route('api.dashboard.summary') }}", { headers:{ 'Accept':'application/json' } });
            return resp.json();
        }

        async function fetchLowStock() {
            const resp = await fetch("{{ route('api.dashboard.lowstock') }}?limit=10", { headers:{ 'Accept':'application/json' } });
            return resp.json();
        }

        async function fetchReceivings() {
            const resp = await fetch("{{ route('api.dashboard.receivings') }}?months=6", { headers:{ 'Accept':'application/json' } });
            return resp.json();
        }

        async function fetchTopSuppliers() {
            const resp = await fetch("{{ route('api.dashboard.topsuppliers') }}?limit=6", { headers:{ 'Accept':'application/json' } });
            return resp.json();
        }

        async function fetchStockValue() {
            const resp = await fetch("{{ route('api.dashboard.stockvalue') }}?months=6", { headers:{ 'Accept':'application/json' } });
            return resp.json();
        }

        function formatCurrency(n) {
            return new Intl.NumberFormat('en-PH', { style:'currency', currency:'PHP', maximumFractionDigits:2 }).format(n);
        }

        async function renderDashboard() {
            try {
                const sum = await fetchSummary();
                document.getElementById('card-total-products').textContent = sum.total_products;
                document.getElementById('card-total-stock').textContent = formatCurrency(sum.total_stock_value || 0);
                document.getElementById('card-low-stock').textContent = sum.low_stock_count;

                const low = await fetchLowStock();
                const list = document.getElementById('lowStockList');
                const smallPreview = document.getElementById('card-low-stock-list');
                list.innerHTML = '';
                if (!low || low.length === 0) {
                    list.innerHTML = '<div class="text-gray-500">No low stock items</div>';
                    if (smallPreview) smallPreview.innerHTML = '<div class="text-gray-500">No low stock</div>';
                } else {
                    low.forEach(p => {
                        const el = document.createElement('div');
                        el.className = 'p-2 border rounded';
                        el.innerHTML = `<div class="font-semibold">${p.part_number || ''} — ${p.name || ''}</div>
                            <div class="text-xs text-gray-600">Supplier: ${p.supplier || ''} — On hand: ${p.ending_inventory ?? 0}</div>`;
                        list.appendChild(el);
                    });

                    // Populate the small preview inside the top summary card (show up to 3 items)
                    if (smallPreview) {
                        smallPreview.innerHTML = '';
                        const previewCount = Math.min(3, low.length);
                        for (let i = 0; i < previewCount; i++) {
                            const p = low[i];
                            const line = document.createElement('div');
                            line.className = 'truncate';
                            line.textContent = `${p.part_number ? p.part_number + ' — ' : ''}${p.name || ''} (${p.ending_inventory ?? 0})`;
                            smallPreview.appendChild(line);
                        }
                    }

                    // 🔹 Quick low stock stats (used by UI badges)
                    const lowCountEl = document.getElementById('lowStockCountBadge');
                    const criticalItemEl = document.getElementById('criticalLowStockItem');
                    const avgOnHandEl = document.getElementById('avgLowStockOnHand');

                    if (lowCountEl) lowCountEl.textContent = low.length || 0;

                    if (low && low.length > 0) {
                        const mostCritical = low.reduce((min, item) => {
                            const qty = item.ending_inventory ?? 0;
                            const minQty = min.ending_inventory ?? 0;
                            return qty < minQty ? item : min;
                        }, low[0]);

                        if (criticalItemEl) {
                            criticalItemEl.textContent = (mostCritical.part_number || '') + ' — ' + (mostCritical.name || '');
                        }

                        const totalOnHand = low.reduce((sum, item) => sum + (item.ending_inventory ?? 0), 0);
                        const avgOnHand = totalOnHand / low.length;
                        if (avgOnHandEl) {
                            avgOnHandEl.textContent = avgOnHand.toFixed(1);
                        }
                    } else {
                        if (criticalItemEl) criticalItemEl.textContent = '—';
                        if (avgOnHandEl)     avgOnHandEl.textContent = '—';
                    }
                }

                const series = await fetchReceivings();
                const receivingsCtx = document.getElementById('receivingsChart').getContext('2d');
                if (receivingsChartObj) {
                    receivingsChartObj.data.labels = series.labels;
                    receivingsChartObj.data.datasets[0].data = series.data;
                    receivingsChartObj.update();
                } else {
                    receivingsChartObj = new Chart(receivingsCtx, {
                        type: 'line',
                        data: {
                            labels: series.labels,
                            datasets: [{
                                label: 'Qty Received',
                                data: series.data,
                                borderColor: '#3b82f6',
                                backgroundColor: 'rgba(59,130,246,0.1)',
                                fill: true,
                            }]
                        },
                        options: { responsive:true, maintainAspectRatio:false }
                    });
                }

                // Stock value trend
                const stockSeries = await fetchStockValue();
                const stockCtx = document.getElementById('stockValueChart').getContext('2d');
                if (stockValueChartObj) {
                    stockValueChartObj.data.labels = stockSeries.labels;
                    stockValueChartObj.data.datasets[0].data = stockSeries.data;
                    stockValueChartObj.update();
                } else {
                    stockValueChartObj = new Chart(stockCtx, {
                        type: 'line',
                        data: {
                            labels: stockSeries.labels,
                            datasets: [{
                                label: 'Stock Value',
                                data: stockSeries.data,
                                borderColor: '#10b981',
                                backgroundColor: 'rgba(16,185,129,0.08)',
                                fill: true,
                            }]
                        },
                        options: { responsive:true, maintainAspectRatio:false, plugins:{tooltip:{callbacks:{label:function(ctx){return formatCurrency(ctx.parsed.y);}}}} }
                    });
                }

                // Top suppliers
                const topp = await fetchTopSuppliers();
                const supLabels = topp.map(s => s.supplier || 'Unknown');
                const supData = topp.map(s => s.value || 0);
                const supCtx = document.getElementById('topSuppliersChart').getContext('2d');

                // 🔹 Quick top-supplier stats
                const supNameEl  = document.getElementById('topSupplierName');
                const supValueEl = document.getElementById('topSupplierValue');

                if (topp && topp.length > 0) {
                    const best = topp.reduce((max, item) => (item.value > (max.value||0) ? item : max), topp[0]);
                    if (supNameEl)  supNameEl.textContent  = best.supplier || '—';
                    if (supValueEl) supValueEl.textContent = formatCurrency(best.value || 0);
                } else {
                    if (supNameEl)  supNameEl.textContent  = '—';
                    if (supValueEl) supValueEl.textContent = '—';
                }
                if (topSuppliersChartObj) {
                    topSuppliersChartObj.data.labels = supLabels;
                    topSuppliersChartObj.data.datasets[0].data = supData;
                    topSuppliersChartObj.update();
                } else {
                    // Use a horizontal bar chart to prevent overlap and provide fixed layout
                    topSuppliersChartObj = new Chart(supCtx, {
                        type: 'bar',
                        data: {
                            labels: supLabels,
                            datasets: [{
                                label: 'Value',
                                data: supData,
                                backgroundColor: supLabels.map((_,i)=>['#6366f1','#10b981','#f59e0b','#ef4444','#8b5cf6','#06b6d4'][i % 6])
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    ticks: { callback: function(v){ return formatCurrency(v); } }
                                },
                                y: {
                                    ticks: { autoSkip: false }
                                }
                            },
                            plugins: {
                                legend: { display: false },
                                tooltip: { callbacks: { label: function(ctx){ return ctx.label + ': ' + formatCurrency(ctx.raw); } } }
                            },
                            elements: { bar: { borderRadius: 6, barThickness: 12, maxBarThickness: 14 } },
                            layout: { padding: { top: 6, bottom: 6, left: 6, right: 6 } }
                        }
                    });
                }
            } catch (e) {
                console.error('Dashboard load failed', e);
            }
        }

        document.addEventListener('DOMContentLoaded', async () => {
            await renderDashboard();

            // wire refresh button
            const btn = document.getElementById('refreshDashboard');
            if (btn) btn.addEventListener('click', async () => {
                btn.disabled = true; btn.textContent = 'Refreshing...';
                try { await renderDashboard(); } catch(e) { console.error(e); }
                btn.disabled = false; btn.textContent = 'Refresh';
            });
        });
    </script>

@endsection
