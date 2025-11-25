<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Receiving;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    public function index()
    {
        return view('pages.dashboard');
    }

    // Return summary cards data as JSON
    public function summary()
    {
        $totalProducts = Product::count();

        $totalStockValue = Product::selectRaw('SUM(COALESCE(ending_inventory,0) * COALESCE(unit_price,0)) as v')
            ->value('v') ?? 0;

        $lowStockCount = Product::whereNotNull('beginning_inventory')
            ->whereColumn('ending_inventory', '<=', 'beginning_inventory')
            ->count();

        $recentReceivings = Receiving::with('product')
            ->latest()
            ->limit(8)
            ->get(['id','product_id','qty_received','created_at']);

        return response()->json([
            'total_products' => (int) $totalProducts,
            'total_stock_value' => (float) $totalStockValue,
            'low_stock_count' => (int) $lowStockCount,
            'recent_receivings' => $recentReceivings,
        ]);
    }

    // Return low-stock list (top N)
    public function lowStock(Request $request)
    {
        $limit = (int) $request->get('limit', 10);
        $rows = Product::where(function($q){
                $q->whereNotNull('beginning_inventory')
                  ->whereColumn('ending_inventory','<=','beginning_inventory');
            })
            // MySQL does not support NULLS LAST syntax; use a CASE expression to push NULLs to the end
            ->orderByRaw("CASE WHEN ending_inventory IS NULL THEN 1 ELSE 0 END, ending_inventory ASC")
            ->limit($limit)
            ->get(['id','part_number','inventory_id','name','supplier','ending_inventory','beginning_inventory']);

        return response()->json($rows);
    }

    // Receivings time series for last N months
    public function receivingsSeries(Request $request)
    {
        $months = (int) $request->get('months', 6);

        // Use start of month to align the buckets (e.g. Jun 1, Jul 1, ...)
        $end = Carbon::now()->endOfMonth();
        $start = (clone $end)->subMonths($months - 1)->startOfMonth();

        // Aggregate qty_received per month from receivings table
        $recvRows = DB::table('receivings')
            ->selectRaw("DATE_FORMAT(date_received, '%Y-%m-01') as month_key, SUM(qty_received) as total_qty")
            ->whereBetween('date_received', [$start->toDateString(), $end->toDateString()])
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->pluck('total_qty', 'month_key')
            ->toArray();

        // Aggregate product counts per month based on when the product record was created
        $prodCounts = DB::table('products')
            ->selectRaw("DATE_FORMAT(DATE(created_at), '%Y-%m-01') as month_key, COUNT(*) as cnt")
            ->whereBetween('created_at', [$start->toDateString(), $end->toDateString()])
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->pluck('cnt', 'month_key')
            ->toArray();

        // Merge totals from receivings (qty) and product counts so newly created products count as activity
        $merged = [];
        foreach ($recvRows as $k => $v) {
            $merged[$k] = (int)$v;
        }
        foreach ($prodCounts as $k => $v) {
            if (isset($merged[$k])) $merged[$k] += (int)$v; else $merged[$k] = (int)$v;
        }

        $labels = [];
        $data = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $key = $cursor->format('Y-m-01');
            $labels[] = $cursor->format('M Y');
            $data[] = isset($merged[$key]) ? (int)$merged[$key] : 0;
            $cursor->addMonth();
        }

        // If still all zeros, fallback to counting newly created products per month
        if (array_sum($data) === 0) {
            $createdRows = DB::table('products')
                ->selectRaw("DATE_FORMAT(DATE(created_at), '%Y-%m-01') as month_key, COUNT(*) as cnt")
                ->whereBetween('created_at', [$start->toDateString(), $end->toDateString()])
                ->groupBy('month_key')
                ->orderBy('month_key')
                ->pluck('cnt', 'month_key')
                ->toArray();

            $data = [];
            $cursor = $start->copy();
            while ($cursor->lte($end)) {
                $key = $cursor->format('Y-m-01');
                $data[] = isset($createdRows[$key]) ? (int)$createdRows[$key] : 0;
                $cursor->addMonth();
            }
        }

        return response()->json(['labels' => $labels, 'data' => $data]);
    }

    // Top suppliers by inventory value
    public function topSuppliers(Request $request)
    {
        $limit = (int) $request->get('limit', 10);
        $rows = Product::select('supplier', DB::raw('SUM(COALESCE(ending_inventory,0) * COALESCE(unit_price,0)) as value'))
            ->groupBy('supplier')
            ->orderByDesc('value')
            ->limit($limit)
            ->get()
            ->map(function($r){
                return ['supplier' => $r->supplier, 'value' => (float)$r->value];
            });

        return response()->json($rows);
    }

    // Stock value trend (monthly) from receivings as a proxy
    public function stockValueTrend(Request $request)
    {
        $months = (int) $request->get('months', 6);

        $end = Carbon::now()->endOfMonth();
        $start = (clone $end)->subMonths($months - 1)->startOfMonth();

        // Aggregate receivings value per month
        $recvRows = DB::table('receivings')
            ->selectRaw("DATE_FORMAT(date_received, '%Y-%m-01') as month_key, SUM(COALESCE(qty_received,0) * COALESCE(unit_price,0)) as total_value")
            ->whereBetween('date_received', [$start->toDateString(), $end->toDateString()])
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->pluck('total_value', 'month_key')
            ->toArray();

        // Aggregate from products as well (use created_at month so newly-added products contribute)
        $prodRows = DB::table('products')
            ->selectRaw("DATE_FORMAT(DATE(created_at), '%Y-%m-01') as month_key, SUM(COALESCE(qty_received,1) * COALESCE(unit_price,0)) as total_value")
            ->whereBetween('created_at', [$start->toDateString(), $end->toDateString()])
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->pluck('total_value', 'month_key')
            ->toArray();

        // Merge totals from both sources so newly created products are visible
        $merged = [];
        foreach ($recvRows as $k => $v) {
            $merged[$k] = (float)$v;
        }
        foreach ($prodRows as $k => $v) {
            if (isset($merged[$k])) $merged[$k] += (float)$v; else $merged[$k] = (float)$v;
        }

        $labels = [];
        $data = [];
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $key = $cursor->format('Y-m-01');
            $labels[] = $cursor->format('M Y');
            $data[] = isset($merged[$key]) ? (float)$merged[$key] : 0;
            $cursor->addMonth();
        }

        // If still all zeros, fallback to summing product unit_price (or qty*unit_price) by created_at month
        if (array_sum($data) == 0) {
            $createdVals = DB::table('products')
                ->selectRaw("DATE_FORMAT(DATE(created_at), '%Y-%m-01') as month_key, SUM(COALESCE(qty_received,1) * COALESCE(unit_price,0)) as total_value")
                ->whereBetween('created_at', [$start->toDateString(), $end->toDateString()])
                ->groupBy('month_key')
                ->orderBy('month_key')
                ->pluck('total_value', 'month_key')
                ->toArray();

            $data = [];
            $cursor = $start->copy();
            while ($cursor->lte($end)) {
                $key = $cursor->format('Y-m-01');
                $data[] = isset($createdVals[$key]) ? (float)$createdVals[$key] : 0;
                $cursor->addMonth();
            }
        }

        return response()->json(['labels' => $labels, 'data' => $data]);
    }

    // Export low-stock items as CSV
    public function exportLowStock()
    {
        $rows = Product::whereNotNull('beginning_inventory')
            ->whereColumn('ending_inventory','<=','beginning_inventory')
            ->withTrashed(false)
            ->get(['part_number','inventory_id','name','supplier','ending_inventory','beginning_inventory']);

        $callback = function() use ($rows) {
            $handle = fopen('php://output','w');
            fputcsv($handle, ['Part Number','Inventory ID','Name','Supplier','On Hand','Reorder Level']);
            foreach ($rows as $r) {
                fputcsv($handle, [
                    $r->part_number,
                    $r->inventory_id,
                    $r->name,
                    $r->supplier,
                    $r->ending_inventory,
                    $r->beginning_inventory,
                ]);
            }
            fclose($handle);
        };

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="low_stock.csv"',
        ];

        return new StreamedResponse($callback, 200, $headers);
    }
}
