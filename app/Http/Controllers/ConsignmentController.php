<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ConsignmentController extends Controller
{
    /**
     * Display a listing of consignment items.
     *
     * This tries to heuristically detect "consignment" products by checking
     * supplier/name/inventory_id for the word "consign" (case-insensitive).
     * If you have a dedicated column for consignment later, replace the
     * condition below with a direct column check.
     */
    public function index(Request $request)
    {
                $query = Product::query();

                // Prefer explicit flag if present; fallback to heuristic matching
                $query->where(function($q){
                        $q->where('is_consignment', true)
                            ->orWhereRaw('LOWER(COALESCE(supplier, "")) LIKE ?', ['%consign%'])
                            ->orWhereRaw('LOWER(COALESCE(name, "")) LIKE ?', ['%consign%'])
                            ->orWhereRaw('LOWER(COALESCE(inventory_id, "")) LIKE ?', ['%consign%']);
                });

        // Allow optional search term (same behaviour as receiving page)
        if ($s = trim(request('q', ''))) {
            $query->where(function($q) use ($s) {
                $q->where('part_number', 'like', "%{$s}%")
                  ->orWhere('inventory_id', 'like', "%{$s}%")
                  ->orWhere('name', 'like', "%{$s}%")
                  ->orWhere('supplier', 'like', "%{$s}%");
            });
        }

        $products = $query->orderBy('name')->get();

        return view('pages.consignment-items', [
            'products' => $products,
        ]);
    }
}
