@extends('layouts.app')

@section('title', 'Delivery Review | Gemarc LAN Based Inventory System')

@section('content')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Delivery Review</h2>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <p class="text-sm text-gray-600 mb-4">Review delivered items posted to RR files. Click a DR row to view details and mark Approved / Rejected.</p>

                    <div class="flex items-center gap-3 mb-3">
                        <div>
                            <input id="dr-search" type="text" placeholder="Search DR, customer or remarks" class="block rounded border-gray-300 px-3 py-2">
                        </div>
                        <div class="flex gap-1">
                            <button type="button" class="filter-tab px-3 py-1 rounded bg-gray-100" data-filter="all">All</button>
                            <button type="button" class="filter-tab px-3 py-1 rounded" data-filter="pending">Pending</button>
                            <button type="button" class="filter-tab px-3 py-1 rounded" data-filter="approved">Approved</button>
                            <button type="button" class="filter-tab px-3 py-1 rounded" data-filter="rejected">Rejected</button>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200" id="dr-table">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">DR Number</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Remarks</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">DR Date</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Items</th>
                                    <th class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($groups as $g)
                                    @php
                                        if ($g->approved_count == $g->item_count) $status = 'approved';
                                        elseif ($g->rejected_count > 0) $status = 'rejected';
                                        else $status = 'pending';
                                    @endphp
                                    <tr class="dr-row cursor-pointer" data-dr="{{ $g->dr_number }}" data-dr-id="{{ $g->sample_id }}" data-status="{{ $status }}">
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $g->dr_number ?: '—' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $g->customer ?: '—' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $g->remarks_preview ? Str::limit($g->remarks_preview,60) : '—' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-700">{{ $g->dr_date ? \Carbon\Carbon::parse($g->dr_date)->format('Y-m-d') : '—' }}</td>
                                        <td class="px-4 py-3 text-sm text-right text-gray-700">{{ $g->item_count }}</td>
                                        <td class="px-4 py-3 text-sm text-right text-gray-800">{{ number_format($g->total_amount ?? 0,2) }}</td>
                                        <td class="px-4 py-3 text-sm">
                                            @if($status == 'approved')
                                                <span class="text-green-700 font-semibold">Approved</span>
                                            @elseif($status == 'rejected')
                                                <span class="text-red-700 font-semibold">Rejected</span>
                                            @else
                                                <span class="text-yellow-700 font-semibold">Pending</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div id="dr-modal" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center p-6">
        <div class="bg-white rounded shadow max-w-3xl w-full">
            <div class="p-4 border-b flex items-center justify-between">
                <h3 id="modal-title" class="font-semibold">DR Details</h3>
                <button id="modal-close" class="text-gray-600">Close</button>
            </div>
            <div class="p-4" id="modal-body">
                <div id="modal-loading">Loading...</div>
            </div>
            <div class="p-4 border-t flex gap-2 justify-end">
                <button id="reject-btn" class="px-3 py-2 bg-red-600 text-white rounded">Reject</button>
                <button id="approve-btn" class="px-3 py-2 bg-green-600 text-white rounded">Approve</button>
                <a id="print-dr" class="px-3 py-2 bg-gray-200 rounded hidden" target="_blank">Print</a>
            </div>
        </div>
    </div>

    <script>
        (function(){
            const rows = document.querySelectorAll('.dr-row');
            const modal = document.getElementById('dr-modal');
            const modalBody = document.getElementById('modal-body');
            const modalTitle = document.getElementById('modal-title');
            const closeBtn = document.getElementById('modal-close');
            const approveBtn = document.getElementById('approve-btn');
            const rejectBtn = document.getElementById('reject-btn');
            const printLink = document.getElementById('print-dr');

            let _lastActiveElement = null;
            function getFocusableElements(container){
                return Array.from(container.querySelectorAll('a[href], button:not([disabled]), textarea, input, select, [tabindex]:not([tabindex="-1"])'))
                    .filter(el => el.offsetParent !== null);
            }

            function showModal() {
                _lastActiveElement = document.activeElement;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                // focus first focusable inside modal
                const dialog = modal.querySelector('.bg-white');
                const focusables = getFocusableElements(dialog);
                if (focusables.length) focusables[0].focus();
            }

            function hideModal() {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                // restore focus
                try { if (_lastActiveElement && typeof _lastActiveElement.focus === 'function') _lastActiveElement.focus(); } catch(e){}
            }

            rows.forEach(r => r.addEventListener('click', async () => {
                const dr = r.dataset.dr;
                modalTitle.textContent = `DR: ${dr || r.dataset.drId}`;
                modalBody.innerHTML = '<div>Loading...</div>';
                showModal();
                // choose details endpoint: if dr present use it, otherwise use sample id endpoint
                let res;
                if (dr) {
                    res = await fetch(`{{ url('/delivery-review') }}/${encodeURIComponent(dr)}`);
                } else {
                    const sid = r.dataset.drId;
                    res = await fetch(`{{ url('/delivery-review/id') }}/${encodeURIComponent(sid)}`);
                }
                if (!res.ok) { modalBody.innerHTML = '<div class="text-red-600">Failed to load details</div>'; return; }
                const data = await res.json();
                const rowsData = data.rows;

                // render details (include remarks column)
                let html = `<div class="mb-3"><strong>Customer:</strong> ${rowsData.length ? (rowsData[0].customer || '—') : '—'}</div>`;
                html += '<table class="min-w-full"><thead><tr><th class="px-2">Product</th><th class="px-2">Part #</th><th class="px-2 text-right">Qty</th><th class="px-2 text-right">Unit Cost</th><th class="px-2 text-right">Amount</th><th class="px-2">Remarks</th></tr></thead><tbody>';
                let total = 0;
                rowsData.forEach(rw => {
                    const qty = parseInt(rw.qty) || 0;
                    const unit = parseFloat(rw.unit_cost) || 0;
                    const amt = unit * qty;
                    total += amt;
                    const itemName = rw.item_name || rw.product && rw.product.name || '—';
                    const part = rw.part_number || '—';
                    const remarks = rw.remarks || '';
                    html += `<tr><td class="px-2">${itemName}</td><td class="px-2">${part}</td><td class="px-2 text-right">${qty}</td><td class="px-2 text-right">${unit.toFixed(2)}</td><td class="px-2 text-right">${amt.toFixed(2)}</td><td class="px-2">${remarks}</td></tr>`;
                });
                html += `</tbody></table><div class="mt-3 text-right font-semibold">Total: ₱${total.toFixed(2)}</div>`;
                modalBody.innerHTML = html;

                // show print link (support sample id print when dr is empty)
                if (dr) {
                    printLink.href = `{{ url('/delivery/print/pdf') }}/${encodeURIComponent(dr)}`;
                } else {
                    const sid = r.dataset.drId;
                    printLink.href = `{{ url('/delivery/print/pdf/sample') }}/${encodeURIComponent(sid)}`;
                }
                printLink.classList.remove('hidden');

                approveBtn.onclick = () => submitApproval(dr,1);
                rejectBtn.onclick = () => submitApproval(dr,0);
            }));

            closeBtn.addEventListener('click', hideModal);

            // Close when clicking on backdrop (outside the dialog)
            modal.addEventListener('click', (ev) => {
                if (ev.target === modal) {
                    hideModal();
                }
            });

            // Keyboard handling: Escape to close, Tab to trap focus inside modal
            document.addEventListener('keydown', (ev) => {
                // only react when modal is visible
                if (modal.classList.contains('hidden')) return;

                if (ev.key === 'Escape' || ev.key === 'Esc') {
                    hideModal();
                    return;
                }

                if (ev.key === 'Tab') {
                    const dialog = modal.querySelector('.bg-white');
                    const focusables = getFocusableElements(dialog);
                    if (!focusables.length) return;
                    const first = focusables[0];
                    const last = focusables[focusables.length - 1];
                    if (ev.shiftKey) {
                        if (document.activeElement === first) {
                            ev.preventDefault();
                            last.focus();
                        }
                    } else {
                        if (document.activeElement === last) {
                            ev.preventDefault();
                            first.focus();
                        }
                    }
                }
            });

            async function submitApproval(dr, approved){
                const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                const res = await fetch(`{{ url('/delivery-review') }}/${encodeURIComponent(dr)}/approve`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token },
                    body: JSON.stringify({ approved })
                });
                if (!res.ok) {
                    alert('Failed to update approval');
                    return;
                }
                const json = await res.json();
                // reload page to reflect status
                location.reload();
            }
        })();
        // Search and filter logic
        (function(){
            const search = document.getElementById('dr-search');
            const tabs = Array.from(document.querySelectorAll('.filter-tab'));
            const tableRows = Array.from(document.querySelectorAll('#dr-table tbody tr'));

            function applyFilter(){
                const q = search.value.trim().toLowerCase();
                const activeTab = document.querySelector('.filter-tab.active');
                const statusFilter = activeTab ? activeTab.dataset.filter : 'all';

                tableRows.forEach(tr => {
                    const dr = (tr.dataset.dr || '').toLowerCase();
                    const cust = (tr.cells[1] && tr.cells[1].textContent || '').toLowerCase();
                    const remarks = (tr.cells[2] && tr.cells[2].textContent || '').toLowerCase();
                    let matches = true;
                    if (q) {
                        matches = dr.includes(q) || cust.includes(q) || remarks.includes(q);
                    }

                    let statusOk = true;
                    if (statusFilter !== 'all') {
                        statusOk = tr.dataset.status === statusFilter;
                    }

                    tr.style.display = (matches && statusOk) ? '' : 'none';
                });
            }

            // default select All
            tabs.forEach(t => {
                t.addEventListener('click', () => {
                    tabs.forEach(x => x.classList.remove('bg-gray-100','font-semibold'));
                    t.classList.add('bg-gray-100','font-semibold');
                    tabs.forEach(x => x.classList.remove('active'));
                    t.classList.add('active');
                    applyFilter();
                });
            });

            search.addEventListener('input', () => applyFilter());

            // initialize
            tabs[0].classList.add('bg-gray-100','font-semibold','active');
        })();
    </script>

@endsection