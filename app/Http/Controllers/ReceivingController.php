<?php

namespace App\Http\Controllers;

use App\Models\Receiving;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReceivingController extends Controller
{
    /**
     * Store a new receiving entry and optional details file.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'qty_received' => 'required|integer',
            'unit_price' => 'nullable|numeric',
            'date_received' => 'nullable|date',
            'details_file' => 'nullable|file|mimes:xlsx,xls,csv',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = Product::find($request->product_id);

        $path = null;
        if ($request->hasFile('details_file')) {
            $path = $request->file('details_file')->store('receivings', 'public');
        }

        $receiving = Receiving::create([
            'product_id' => $product->id,
            'fo_number' => $request->fo_number,
            'date_received' => $request->date_received,
            'qty_received' => $request->qty_received,
            'unit_price' => $request->unit_price,
            'beginning_inventory' => $request->beginning_inventory,
            'ending_inventory' => $request->ending_inventory,
            'details_file_path' => $path,
        ]);

        // Update product inventory: increment ending_inventory by qty_received when not provided
        if ($product) {
            if (is_null($product->ending_inventory)) $product->ending_inventory = 0;
            $product->ending_inventory = $product->ending_inventory + (int) $request->qty_received;
            $product->save();
        }

        return response()->json($receiving);
    }

    /**
     * Show receiving entry page with available products.
     */
    public function index()
    {
        $products = Product::orderBy('name')->get();
        return view('pages.receiving-entry', compact('products'));
    }

    /**
     * Export receiving report as CSV stream combining receive rows and product info.
     */
    public function export()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="receiving_report.csv"',
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');
            // header row
            fputcsv($handle, [
                'Receiving ID','Product ID','Part Number','Inventory ID','Name','Supplier','F.O #','Date Received','Qty. Received','Unit Price','Beginning Inventory','Ending Inventory','Details File'
            ]);

            $rows = Receiving::with('product')->orderBy('created_at','desc')->get();
            foreach ($rows as $r) {
                fputcsv($handle, [
                    $r->id,
                    $r->product_id,
                    $r->product->part_number ?? '',
                    $r->product->inventory_id ?? '',
                    $r->product->name ?? '',
                    $r->product->supplier ?? '',
                    $r->fo_number ?? '',
                    $r->date_received ?? '',
                    $r->qty_received ?? '',
                    $r->unit_price ?? '',
                    $r->beginning_inventory ?? '',
                    $r->ending_inventory ?? '',
                    $r->details_file_path ? Storage::url($r->details_file_path) : '',
                ]);
            }

            fclose($handle);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
