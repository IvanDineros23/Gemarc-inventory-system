<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inventory Report | Print</title>
    <style>
        /* Use DejaVu Sans for PDF (Dompdf supports DejaVu glyphs) */
        body { font-family: 'DejaVu Sans', Arial, sans-serif; background: #fff; color: #222; margin: 0; padding: 0; }
        .container { max-width: 700px; margin: 40px auto; background: #fff; padding: 32px 40px; border-radius: 10px; box-shadow: 0 2px 8px #0001; }
        .signatures { display: flex; justify-content: space-between; gap: 16px; margin-top: 28px; }
        .signature-column { width: 48%; }
        .sig-entry { margin-bottom: 14px; }
        .sig-entry .sig-line { border-bottom: 1px solid #222; height: 48px; margin-bottom: 8px; }
        .sig-entry .sig-label { font-size: 0.95rem; color: #333; }
        .header { text-align: center; margin-bottom: 32px; }
        .logo { height: 60px; margin-bottom: 8px; }
        .title { font-size: 2rem; font-weight: bold; margin-bottom: 4px; }
        .subtitle { font-size: 1.1rem; color: #555; margin-bottom: 16px; }
        .date { font-size: 0.95rem; color: #888; margin-bottom: 24px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 32px; }
        th, td { padding: 10px 8px; text-align: left; }
        th { background: #f3f3f3; font-size: 1.05rem; }
        tr:not(:last-child) td { border-bottom: 1px solid #e0e0e0; }
        .brand { font-weight: bold; font-size: 1.1rem; }
        .total-row td { font-weight: bold; font-size: 1.1rem; border-top: 2px solid #222; }
        @media print {
            body { background: #fff !important; }
            .container { box-shadow: none; border-radius: 0; margin: 0; padding: 0 0 0 0; }
            .print-btn { display: none !important; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            @php
                $logoPath = (isset($forPdf) && $forPdf)
                    ? public_path('images/gemarclogo.png')
                    : asset('images/gemarclogo.png');
            @endphp
            <img src="{{ $logoPath }}" alt="Gemarc Logo" class="logo">
            <div class="title">Inventory Report</div>
            <div class="subtitle">Brand Grand Totals</div>
            <div class="date">Printed: {{ now()->setTimezone('Asia/Manila')->format('F j, Y — h:i A') }}</div>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Brand</th>
                    <th style="text-align:right">Grand Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($brands as $brand)
                <tr>
                    <td class="brand">{{ $brand['name'] }}</td>
                    <td style="text-align:right">&#x20B1; {{ number_format($brand['total'], 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td style="text-align:right">Grand Total:</td>
                    <td style="text-align:right">&#x20B1; {{ number_format($grandTotal, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Signature placeholders: two stacked per column -->
        <div class="signatures">
            <div class="signature-column">
                <div class="sig-entry">
                    <div class="sig-line"></div>
                    <div class="sig-label">Prepared By</div>
                </div>
                <div class="sig-entry">
                    <div class="sig-line"></div>
                    <div class="sig-label">Checked By</div>
                </div>
            </div>
            <div class="signature-column" style="text-align: right;">
                <div class="sig-entry" style="text-align: left;">
                    <div class="sig-line"></div>
                    <div class="sig-label">Approved By</div>
                </div>
                <div class="sig-entry" style="text-align: left;">
                    <div class="sig-line"></div>
                    <div class="sig-label">Received By</div>
                </div>
            </div>
        </div>

        @if (!isset($forPdf) || !$forPdf)
        <a href="{{ route('inventory.report.download') }}" class="print-btn" style="margin: 24px auto 12px; display: inline-block; padding: 10px 30px; background: #16a34a; color: #fff; border: none; border-radius: 5px; font-size: 1rem; cursor: pointer; text-align: center; text-decoration: none;">Print</a>
        @endif
    </div>
</body>
</html>
