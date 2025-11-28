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
                // Use date_received when available; fallback to created_at when date_received is NULL
                $dateExpression = "COALESCE(receivings.date_received, receivings.created_at)";

                $monthly = DB::table('receivings')
                    ->leftJoin('products', 'receivings.product_id', '=', 'products.id')
                    ->selectRaw("products.id as product_id, products.name as product_name, YEAR({$dateExpression}) as year, MONTH({$dateExpression}) as month, SUM(receivings.qty_received) as total")
                    ->groupBy('products.id', 'products.name', DB::raw("YEAR({$dateExpression})"), DB::raw("MONTH({$dateExpression})"))
                    ->orderBy('year', 'desc')
                    ->orderBy('month', 'desc')
                    ->get();

                $source = 'receivings (using date_received or created_at fallback)';
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
