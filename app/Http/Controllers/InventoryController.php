<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();
        $query->orderBy('supplier');
        if (Schema::hasColumn('products', 'brand')) {
            $query->orderBy('brand');
        }
        $products = $query->get();

        $suppliers = [];
        $grandTotal = 0;

        foreach ($products as $product) {
            $supplierName = $product->supplier ?? 'Unspecified';
            $brandName = $product->brand ?? 'Unspecified';

            if (!isset($suppliers[$supplierName])) {
                $suppliers[$supplierName] = [
                    'name' => $supplierName,
                    'brands' => [],
                    'total' => 0,
                ];
            }

            if (!isset($suppliers[$supplierName]['brands'][$brandName])) {
                $suppliers[$supplierName]['brands'][$brandName] = [
                    'name' => $brandName,
                    'products' => [],
                    'total' => 0,
                ];
            }

            $rowTotal = $product->total ?? (($product->qty_received ?? 0) * ($product->unit_price ?? 0));

            $productData = [
                'id' => $product->id,
                'part_number' => $product->part_number,
                'inventory_id' => $product->inventory_id,
                'name' => $product->name,
                'brand' => $brandName,
                'qty' => $product->ending_inventory ?? $product->qty_received ?? 0,
                'unit' => $product->unit ?? '',
                'unit_price' => number_format((float)($product->unit_price ?? 0), 2, '.', ''),
                'total' => number_format((float)$rowTotal, 2, '.', ''),
            ];

            $suppliers[$supplierName]['brands'][$brandName]['products'][] = $productData;
            $suppliers[$supplierName]['brands'][$brandName]['total'] += (float)$rowTotal;
            $suppliers[$supplierName]['total'] += (float)$rowTotal;
            $grandTotal += (float)$rowTotal;
        }

        // Convert brands arrays to indexed arrays for easier JSON encoding in blade
        foreach ($suppliers as $sName => $sData) {
            $suppliers[$sName]['brands'] = array_values($suppliers[$sName]['brands']);
            // format totals
            $suppliers[$sName]['total'] = number_format($suppliers[$sName]['total'], 2, '.', '');
            foreach ($suppliers[$sName]['brands'] as $bIndex => $bData) {
                $suppliers[$sName]['brands'][$bIndex]['total'] = number_format($bData['total'], 2, '.', '');
            }
        }

        $grandTotal = number_format($grandTotal, 2, '.', '');

        return view('pages.inventory-per-supplier', [
            'suppliers' => $suppliers,
            'grandTotal' => $grandTotal,
        ]);
    }

    public function print(Request $request)
    {
        // Reuse the same data preparation as index
        $query = Product::query();
        $query->orderBy('supplier');
        if (Schema::hasColumn('products', 'brand')) {
            $query->orderBy('brand');
        }
        $products = $query->get();

        $suppliers = [];
        $grandTotal = 0;

        foreach ($products as $product) {
            $supplierName = $product->supplier ?? 'Unspecified';
            $brandName = $product->brand ?? 'Unspecified';

            if (!isset($suppliers[$supplierName])) {
                $suppliers[$supplierName] = [
                    'name' => $supplierName,
                    'brands' => [],
                    'total' => 0,
                ];
            }

            if (!isset($suppliers[$supplierName]['brands'][$brandName])) {
                $suppliers[$supplierName]['brands'][$brandName] = [
                    'name' => $brandName,
                    'products' => [],
                    'total' => 0,
                ];
            }

            $rowTotal = $product->total ?? (($product->qty_received ?? 0) * ($product->unit_price ?? 0));

            $productData = [
                'id' => $product->id,
                'part_number' => $product->part_number,
                'inventory_id' => $product->inventory_id,
                'name' => $product->name,
                'brand' => $brandName,
                'qty' => $product->ending_inventory ?? $product->qty_received ?? 0,
                'unit' => $product->unit ?? '',
                'unit_price' => number_format((float)($product->unit_price ?? 0), 2, '.', ''),
                'total' => number_format((float)$rowTotal, 2, '.', ''),
            ];

            $suppliers[$supplierName]['brands'][$brandName]['products'][] = $productData;
            $suppliers[$supplierName]['brands'][$brandName]['total'] += (float)$rowTotal;
            $suppliers[$supplierName]['total'] += (float)$rowTotal;
            $grandTotal += (float)$rowTotal;
        }

        foreach ($suppliers as $sName => $sData) {
            $suppliers[$sName]['brands'] = array_values($suppliers[$sName]['brands']);
            $suppliers[$sName]['total'] = number_format($suppliers[$sName]['total'], 2, '.', '');
            foreach ($suppliers[$sName]['brands'] as $bIndex => $bData) {
                $suppliers[$sName]['brands'][$bIndex]['total'] = number_format($bData['total'], 2, '.', '');
            }
        }

        $grandTotal = number_format($grandTotal, 2, '.', '');

        return view('pages.inventory-per-supplier-print', [
            'suppliers' => $suppliers,
            'grandTotal' => $grandTotal,
        ]);
    }
}
