@section('title', 'Inventory Status per Supplier - Print')
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inventory Status per Supplier - Print</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #222;
            margin: 24px;
        }

        h1, h2, h3 {
            margin: 0;
        }

        /* Header: logo on top, then title + subtitle */
        header {
            text-align: center;
            margin-bottom: 18px;
        }

        .logo {
            max-width: 150px;
            height: auto;
            display: block;
            margin: 0 auto 6px auto;
        }

        .title {
            text-align: center;
        }

        .title h1 {
            font-size: 22px;
            letter-spacing: 0.5px;
        }

        .subtitle {
            margin-top: 4px;
            color: #444;
            font-size: 13px;
        }

        .supplier {
            margin-bottom: 22px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px 8px;
            text-align: left;
            font-size: 13px;
        }

        th {
            background: #f3f3f3;
        }

        .right {
            text-align: right;
        }

        /* ✅ para hindi mag-stack yung ₱ at value */
        .money {
            white-space: nowrap;
        }

        .supplier-header {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            margin-bottom: 8px;
        }

        .supplier-name {
            font-size: 16px;
            font-weight: 700;
        }

        .brand-name {
            font-size: 14px;
            font-weight: 700;
            margin: 10px 0 4px 0;
        }

        .brand-block {
            margin-bottom: 10px;
        }

        .grand-total {
            margin-top: 20px;
            font-weight: 700;
            font-size: 18px;
            text-align: right;
        }

        .footer-meta {
            margin-top: 18px;
            font-size: 12px;
            color: #444;
            display: flex;
            justify-content: space-between;
        }

        @media print {
            a { display: none }
            body { margin: 8mm; }
        }
    </style>
</head>
<body>
    <header>
        <img src="{{ asset('images/gemarclogo.png') }}" alt="Gemarc" class="logo" />

        <div class="title">
            <h1>Inventory Status</h1>
            <div class="subtitle">Detailed Inventory per Supplier</div>
        </div>
    </header>

    <main>
        @foreach($suppliers as $supplier)
            <section class="supplier">
                <div class="supplier-header">
                    <div class="supplier-name">{{ strtoupper($supplier['name']) }}</div>
                    <div class="right">
                        Supplier Total: ₱ {{ number_format((float) $supplier['total'], 2) }}
                    </div>
                </div>

                @foreach($supplier['brands'] as $brand)
                    <div class="brand-block">
                        <div class="brand-name">
                            {{ strtoupper($brand['name']) }} — Brand Total: ₱ {{ number_format((float) $brand['total'], 2) }}
                        </div>

                        <table>
                            <thead>
                                <tr>
                                    <th style="width:12%">Part #</th>
                                    <th style="width:14%">Inventory ID</th>
                                    <th style="width:40%">Name</th>
                                    <th style="width:6%" class="right">Qty</th>
                                    <th style="width:8%">Unit</th>
                                    <th style="width:10%" class="right money">Unit Price</th>
                                    <th style="width:10%" class="right money">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($brand['products'] as $p)
                                    <tr>
                                        <td>{{ strtoupper($p['part_number'] ?? '') }}</td>
                                        <td>{{ strtoupper($p['inventory_id'] ?? '') }}</td>
                                        <td>{{ strtoupper($p['name'] ?? '') }}</td>
                                        <td class="right">{{ $p['qty'] }}</td>
                                        <td>{{ $p['unit'] }}</td>
                                        <td class="right money">
                                            ₱ {{ number_format((float) $p['unit_price'], 2) }}
                                        </td>
                                        <td class="right money">
                                            ₱ {{ number_format((float) $p['total'], 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
            </section>
        @endforeach

        <div class="grand-total">
            Grand Total: ₱ {{ number_format((float) $grandTotal, 2) }}
        </div>

        <div class="footer-meta">
            <div></div>
            <div style="text-align:right;">
                Printed:
                {{ \Carbon\Carbon::now()->setTimezone('Asia/Manila')->format('F j, Y g:i A') }}
            </div>
        </div>
    </main>

    <script>
        window.addEventListener('load', function () {
            window.print();
        });
    </script>
</body>
</html>
