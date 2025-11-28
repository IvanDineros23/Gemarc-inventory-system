<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ReorderController extends Controller
{
    /**
     * Display the re-order level page with low-stock notifications.
     */
    public function index(Request $request)
    {
        // Define threshold for low stock
        $lowStockThreshold = 5;

        // Heuristic for "fast moving" items: recently received batches of at least 20 units
        $fastMovingQty = 20;

        // All products that are low on stock (ending_inventory <= threshold)
        $lowStock = Product::whereNotNull('ending_inventory')
            ->where('ending_inventory', '<=', $lowStockThreshold)
            ->orderBy('ending_inventory', 'asc')
            ->get();

        // Among low-stock products, highlight those that are likely fast-moving
        $lowFastMoving = $lowStock->filter(function ($p) use ($fastMovingQty) {
            return $p->qty_received !== null && $p->qty_received >= $fastMovingQty;
        })->values();

        return view('pages.reorder-level-entry', [
            'lowStock' => $lowStock,
            'lowFastMoving' => $lowFastMoving,
            'lowStockThreshold' => $lowStockThreshold,
        ]);
    }
}
