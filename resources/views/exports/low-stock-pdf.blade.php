<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Low Stock Items</title>
    <style>
        @page { margin: 20mm; }
        body { font-family: DejaVu Sans, Arial, Helvetica, sans-serif; font-size:11px; color:#222; margin:0; padding:0; }
        .header { text-align:center; margin-bottom:24px; }
        .logo { height:60px; display:block; margin:0 auto 12px; }
        h1 { margin: 12px 0 8px; font-size:18px; font-weight:bold; text-align:center; }
        .subtitle { font-size:11px; color:#555; margin-bottom:20px; text-align:center; }
        table { width:100%; border-collapse: collapse; margin-top:10px; font-size:9px; table-layout:fixed; }
        th, td { border:1px solid #999; padding:6px 8px; word-wrap:break-word; overflow:hidden; }
        th { background:#e8e8e8; text-align:left; font-weight:bold; }
        .right { text-align:right; }
        .signatures { margin-top:60px; width:100%; }
        .sig-row { display:table; width:100%; }
        .sig { display:table-cell; width:33.33%; text-align:center; padding:0 20px; }
        .sig-line { border-top:1px solid #000; margin:0 auto 6px; width:160px; }
        .sig-label { font-size:10px; color:#333; }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/gemarclogo.png') }}" alt="Gemarc Logo" class="logo" />
        <h1>Low Stock Items</h1>
        <div class="subtitle">On-hand ≤ {{ $threshold }} — Generated: {{ now()->timezone('Asia/Manila')->format('Y-m-d h:i:s A') }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:5%">ID</th>
                <th style="width:12%">Part #</th>
                <th style="width:16%">Inventory ID</th>
                <th style="width:30%">Name</th>
                <th style="width:12%">Brand</th>
                <th style="width:15%">Supplier</th>
                <th style="width:10%" class="right">On Hand</th>
            </tr>
        </thead>
        <tbody>
            @forelse($items as $item)
                <tr>
                    <td>{{ $item->id }}</td>
                    <td>{{ $item->part_number }}</td>
                    <td>{{ $item->inventory_id }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->brand }}</td>
                    <td>{{ $item->supplier }}</td>
                    <td class="right">{{ $item->ending_inventory }}</td>
                </tr>
            @empty
                <tr><td colspan="7" style="padding:18px;text-align:center">No low stock items found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="signatures">
        <div class="sig-row">
            <div class="sig">
                <div class="sig-line"></div>
                <div class="sig-label">Prepared By</div>
            </div>
            <div class="sig">
                <div class="sig-line"></div>
                <div class="sig-label">Checked By</div>
            </div>
            <div class="sig">
                <div class="sig-line"></div>
                <div class="sig-label">Approved By</div>
            </div>
        </div>
    </div>
</body>
</html>
