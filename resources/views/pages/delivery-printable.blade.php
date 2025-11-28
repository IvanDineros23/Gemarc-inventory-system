@php
    use Carbon\Carbon;
    $first = $rows->first();
    $drLabel = $dr_number ?? ($first->dr_number ?? 'N/A');
    // Parse DR date or fallback date, then convert to Asia/Manila timezone
    $drDate = null;
    if (!empty($first->dr_date)) {
        $drDate = Carbon::parse($first->dr_date)->setTimezone('Asia/Manila');
    } elseif (!empty($first->date)) {
        $drDate = Carbon::parse($first->date)->setTimezone('Asia/Manila');
    }
    $total = $rows->sum(function($r){ return (float)($r->unit_cost ?? 0) * (int)($r->qty ?? 0); });
    // Use Asia/Manila for generated timestamp to match local time
    $generatedAt = Carbon::now('Asia/Manila');
@endphp

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Delivery Receipt - {{ $drLabel }}</title>
    <style>
        /* A4 page sizing for DOMPDF */
        @page { size: A4 portrait; margin: 15mm; }
        html, body { margin:0; padding:0; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; color: #111; font-size:12px; }
        .container { width:100%; max-width:180mm; margin:0 auto; padding:6mm 0; }
        .header { display:block; text-align:center; margin-bottom:12mm; }
        .logo { width: 180px; margin:0 auto 6px auto; }
        .logo img { display:block; margin:0 auto; width:180px; height:auto; }
        .company { font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; font-size:16px; font-weight:700; letter-spacing:0.4px; text-transform:uppercase; }
        .company-sub { font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif; font-size:11px; color:#555; margin-top:6px; line-height:1.2; }
        .title { text-align:center; margin-top:4mm; margin-bottom:6mm; font-size:16px; font-weight:700; }
        .details { margin-bottom:10mm; }
        .details .left { float:left; width:60%; }
        .details .right { float:right; width:35%; text-align:right; }
        table { width:100%; border-collapse: collapse; margin-top:6px; table-layout: fixed; word-wrap: break-word; }
        table th, table td { border: 1px solid #ddd; padding:6px 8px; font-size:11px; vertical-align:top; }
        table th { background:#f4f4f4; text-align:left; }
        td.amt, th.amt { text-align:right; }
        td.remarks { word-break:break-word; max-width:60mm; }
        .total { text-align:right; margin-top:8px; font-weight:700; }
        .footer { margin-top:18px; font-size:11px; color:#555; border-top:1px solid #eee; padding-top:6px; }
        /* avoid breaking rows across pages */
        tr { page-break-inside: avoid; }
        thead { display:table-header-group; }
        tfoot { display:table-footer-group; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">
                @php $logoPath = public_path('images/gemarclogo.png'); @endphp
                @if(file_exists($logoPath))
                    {{-- Use file:// prefix so DOMPDF can access the local file path reliably --}}
                    <img src="file://{{ $logoPath }}" alt="Gemarc">
                @else
                    <div style="font-weight:700;">Gemarc Logo</div>
                @endif
            </div>

            <div class="title">DELIVERY RECEIPT</div>

            <div class="company-sub">
                Office Address<br>
                No. 15 Chile St. Ph1 Greenheights Subdivision, Concepcion 1, Marikina City, Philippines 1807<br>
                +63 909 087 9416 | +63 928 395 3532 | +63 918 905 8316
            </div>
        </div>

        

        <div class="details">
            <div class="left">
                <div><strong>DR #:</strong> {{ $drLabel }}</div>
                <div><strong>Customer:</strong> {{ $first->customer ?? '—' }}</div>
                <div><strong>Intended to:</strong>
                    @php
                        $intd = $first->intended_to ?? '';
                        $dec = json_decode($intd, true);
                    @endphp
                    @if(is_array($dec))
                        {{ implode(', ', $dec) }}
                    @else
                        {{ $intd ?: '—' }}
                    @endif
                </div>
            </div>
            <div class="right">
                <div><strong>DR Date:</strong> {{ $drDate ? $drDate->format('Y-m-d H:i') : '—' }}</div>
            </div>
            <div style="clear:both;"></div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Part #</th>
                    <th>Item</th>
                    <th style="text-align:right;">Qty</th>
                    <th style="text-align:right;">Unit Cost</th>
                    <th style="text-align:right;">Amount</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $r)
                    @php
                        $qty = (int)($r->qty ?? 0);
                        $unit = (float)($r->unit_cost ?? 0);
                        $amt = $qty * $unit;
                        $itemName = $r->item_name ?: ($r->product->name ?? '—');
                    @endphp
                    <tr>
                        <td>{{ $r->part_number ?? '—' }}</td>
                        <td>{{ $itemName }}</td>
                        <td style="text-align:right;">{{ $qty }}</td>
                        <td style="text-align:right;">{{ number_format($unit,2) }}</td>
                        <td style="text-align:right;">{{ number_format($amt,2) }}</td>
                        <td>{{ $r->remarks ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total">Total: ₱{{ number_format($total,2) }}</div>

        <div class="footer">Generated on {{ $generatedAt->format('Y-m-d H:i:s') }}</div>
    </div>
</body>
</html>
