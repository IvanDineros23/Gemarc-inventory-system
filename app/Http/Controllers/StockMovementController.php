<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Product;

class StockMovementController extends Controller
{
    /**
     * Display monthly delivered items per product.
     */
    public function index(Request $request)
    {
        // Try to read from a "deliveries" table if it exists (common name for outgoing stock)
        if (Schema::hasTable('deliveries')) {
            $monthly = DB::table('deliveries')
                ->leftJoin('products', 'deliveries.product_id', '=', 'products.id')
                ->selectRaw('products.id as product_id, products.name as product_name, YEAR(deliveries.date) as year, MONTH(deliveries.date) as month, SUM(deliveries.qty) as total')
                ->groupBy('products.id', 'products.name', DB::raw('YEAR(deliveries.date)'), DB::raw('MONTH(deliveries.date)'))
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();

            $source = 'deliveries';

        } else {
            // Fallback: if there's no deliveries table, attempt to use receivings as example data
            if (Schema::hasTable('receivings')) {
                $monthly = DB::table('receivings')
                    ->leftJoin('products', 'receivings.product_id', '=', 'products.id')
                    ->selectRaw('products.id as product_id, products.name as product_name, YEAR(receivings.date_received) as year, MONTH(receivings.date_received) as month, SUM(receivings.qty_received) as total')
                    ->whereNotNull('receivings.date_received')
                    ->groupBy('products.id', 'products.name', DB::raw('YEAR(receivings.date_received)'), DB::raw('MONTH(receivings.date_received)'))
                    ->orderBy('year', 'desc')
                    ->orderBy('month', 'desc')
                    ->get();

                $source = 'receivings (example)';
            } else {
                $monthly = collect();
                $source = null;
            }
        }

        // Optionally filter by product or year/month from request
        if ($request->filled('product_id')) {
            $monthly = $monthly->where('product_id', (int) $request->product_id)->values();
        }

        return view('pages.stock-movement', [
            'monthly' => $monthly,
            'source' => $source,
            'products' => Product::orderBy('name')->get(),
        ]);
    }
}
