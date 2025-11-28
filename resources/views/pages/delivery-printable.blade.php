@extends('layouts.app')

@section('title', 'Delivery Printable')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-lg font-semibold mb-2">Delivery Receipt — DR #: {{ $dr_number }}</h2>
        @php
            $first = $rows->first();
        @endphp
        <div class="mb-4">
            <div><strong>Customer:</strong> {{ $first->customer ?? '—' }}</div>
            <div><strong>DR Date:</strong> {{ optional($first->dr_date)->format('Y-m-d H:i') ?? ($first->date ? \Carbon\Carbon::parse($first->date)->format('Y-m-d H:i') : '') }}</div>
            <div><strong>Intended to:</strong>
                @php
                    $intended = $first->intended_to;
                    $decoded = null;
                    if ($intended) {
                        $decoded = json_decode($intended, true);
                    }
                @endphp
                @if(is_array($decoded))
                    <div>
                        @foreach($decoded as $it)
                            <div>{{ $it }}</div>
                        @endforeach
                    </div>
                @else
                    {{ $first->intended_to ?? '—' }}
                @endif
            </div>
        </div>

        <table class="min-w-full table-auto border-collapse">
            <thead>
                <tr class="border-b">
                    <th class="px-3 py-2 text-left">Part #</th>
                    <th class="px-3 py-2 text-left">Item</th>
                    <th class="px-3 py-2 text-right">Qty</th>
                    <th class="px-3 py-2 text-right">Unit Cost</th>
                    <th class="px-3 py-2 text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $r)
                    <tr class="border-b">
                        <td class="px-3 py-2">{{ $r->part_number }}</td>
                        <td class="px-3 py-2">{{ $r->item_name }}</td>
                        <td class="px-3 py-2 text-right">{{ $r->qty }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format($r->unit_cost ?? 0,2) }}</td>
                        <td class="px-3 py-2 text-right">{{ number_format(($r->unit_cost ?? 0) * $r->qty,2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="mt-4 text-right font-semibold">
            Total: ₱{{ number_format($rows->sum(function($r){ return ($r->unit_cost ?? 0) * $r->qty; }),2) }}
        </div>

        <div class="mt-6 flex gap-3 justify-end">
            <a href="javascript:window.print()" class="inline-block px-4 py-2 bg-blue-600 text-white rounded">Print</a>
            <a href="{{ route('delivery.entry') }}" class="inline-block px-4 py-2 bg-gray-200">Done</a>
        </div>
    </div>
@endsection
