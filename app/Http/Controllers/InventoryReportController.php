<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

use Illuminate\Support\Facades\Schema;
use Barryvdh\DomPDF\Facade\Pdf;

class InventoryReportController extends Controller
{
    // Shows the printable HTML view in the browser (not a PDF download)
    public function print(Request $request)
    {
        $query = Product::query();
        if (Schema::hasColumn('products', 'brand')) {
            $query->orderBy('brand');
        }
        $query->orderBy('name');
        $products = $query->get();

        $brands = [];
        $grandTotal = 0;

        foreach ($products as $product) {
            $brand = trim((string)($product->brand ?? ''));
            $brandName = $brand !== '' ? $brand : 'Unspecified';
            if (!isset($brands[$brandName])) {
                $brands[$brandName] = [
                    'name' => $brandName,
                    'products' => [],
                    'total' => 0,
                ];
            }
            $rowTotal = ($product->ending_inventory ?? $product->qty_received ?? 0) * ($product->unit_price ?? 0);
            $brands[$brandName]['total'] += (float)$rowTotal;
            $grandTotal += (float)$rowTotal;
        }
        foreach ($brands as $bName => $bData) {
            $brands[$bName]['total'] = number_format($bData['total'], 2, '.', '');
        }
        $brands = array_values($brands);
        $grandTotal = number_format($grandTotal, 2, '.', '');

        return view('pages.inventory-report-print', [
            'brands' => $brands,
            'grandTotal' => $grandTotal,
            'forPdf' => false,
        ]);
    }

    // Generates and returns a PDF download (keeps PDF generation separate)
    public function download(Request $request)
    {
        $query = Product::query();
        if (Schema::hasColumn('products', 'brand')) {
            $query->orderBy('brand');
        }
        $query->orderBy('name');
        $products = $query->get();

        $brands = [];
        $grandTotal = 0;

        foreach ($products as $product) {
            $brand = trim((string)($product->brand ?? ''));
            $brandName = $brand !== '' ? $brand : 'Unspecified';
            if (!isset($brands[$brandName])) {
                $brands[$brandName] = [
                    'name' => $brandName,
                    'products' => [],
                    'total' => 0,
                ];
            }
            $rowTotal = ($product->ending_inventory ?? $product->qty_received ?? 0) * ($product->unit_price ?? 0);
            $brands[$brandName]['total'] += (float)$rowTotal;
            $grandTotal += (float)$rowTotal;
        }
        foreach ($brands as $bName => $bData) {
            $brands[$bName]['total'] = number_format($bData['total'], 2, '.', '');
        }
        $brands = array_values($brands);
        $grandTotal = number_format($grandTotal, 2, '.', '');

        $pdf = Pdf::loadView('pages.inventory-report-print', [
            'brands' => $brands,
            'grandTotal' => $grandTotal,
            'forPdf' => true,
        ]);

        return $pdf->download('inventory-report.pdf');
    }

    public function index(Request $request)
    {
        $query = Product::query();
        if (Schema::hasColumn('products', 'brand')) {
            $query->orderBy('brand');
        }
        $query->orderBy('name');
        $products = $query->get();

        $brands = [];
        $grandTotal = 0;

        foreach ($products as $product) {
            $brand = trim((string)($product->brand ?? ''));
            $brandName = $brand !== '' ? $brand : 'Unspecified';
            if (!isset($brands[$brandName])) {
                $brands[$brandName] = [
                    'name' => $brandName,
                    'products' => [],
                    'total' => 0,
                ];
            }
            $rowTotal = ($product->ending_inventory ?? $product->qty_received ?? 0) * ($product->unit_price ?? 0);
            $brands[$brandName]['products'][] = [
                'id' => $product->id,
                'part_number' => $product->part_number,
                'inventory_id' => $product->inventory_id,
                'name' => $product->name,
                'qty' => $product->ending_inventory ?? $product->qty_received ?? 0,
                'unit' => $product->unit ?? '',
                'unit_price' => number_format((float)($product->unit_price ?? 0), 2, '.', ''),
                'total' => number_format((float)$rowTotal, 2, '.', ''),
            ];
            $brands[$brandName]['total'] += (float)$rowTotal;
            $grandTotal += (float)$rowTotal;
        }

        // Format totals and convert brands to indexed array
        foreach ($brands as $bName => $bData) {
            $brands[$bName]['total'] = number_format($bData['total'], 2, '.', '');
        }
        $brands = array_values($brands);
        $grandTotal = number_format($grandTotal, 2, '.', '');

        return view('pages.inventory-report', [
            'brands' => $brands,
            'grandTotal' => $grandTotal,
        ]);
    }
}
