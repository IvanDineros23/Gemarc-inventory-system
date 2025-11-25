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

        // Load product relation so AJAX responses include product details
        $receiving->load('product');

        return response()->json($receiving);
    }

    /**
     * Show receiving entry page with available products.
     */
    public function index()
    {
        $products = Product::orderBy('name')->get();
        $manualReceivings = Receiving::whereNull('product_id')
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // recently received items linked to products
        $receivedItems = Receiving::whereNotNull('product_id')
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('pages.receiving-entry', compact('products','manualReceivings','receivedItems'));
    }

    /**
     * Store a manual receiving entry (not linked to an existing product) submitted by users.
     */
    public function manualStore(Request $request)
    {
        $validated = $request->validate([
            'part_number' => 'nullable|string|max:255',
            'item_name' => 'nullable|string|max:255',
            'supplier' => 'nullable|string|max:255',
            'date_received' => 'nullable|date',
            'fo_number' => 'nullable|string|max:255',
            'quantity' => 'nullable|integer',
            'unit_cost' => 'nullable|numeric',
        ]);

        // try to find existing product by part_number or name
        $product = null;
        if (!empty($validated['part_number'])) {
            $product = Product::where('part_number', $validated['part_number'])->first();
        }
        if (!$product && !empty($validated['item_name'])) {
            $product = Product::where('name', $validated['item_name'])->first();
        }

        $receiving = Receiving::create([
            'product_id' => $product ? $product->id : null,
            'fo_number' => $validated['fo_number'] ?? null,
            'date_received' => $validated['date_received'] ?? null,
            'qty_received' => $validated['quantity'] ?? null,
            'unit_price' => $validated['unit_cost'] ?? null,
            'beginning_inventory' => null,
            'ending_inventory' => null,
            'details_file_path' => null,
        ]);

        // If we found a matching product, update its ending_inventory
        if ($product && ($validated['quantity'] ?? null) !== null) {
            if (is_null($product->ending_inventory)) $product->ending_inventory = 0;
            $product->ending_inventory = $product->ending_inventory + (int)$validated['quantity'];
            $product->save();
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($receiving, 201);
        }

        return redirect()->route('receiving.entry')->with('success', 'Manual receiving saved.');
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

    /**
     * Update an existing receiving entry (AJAX-friendly).
     */
    public function update(Request $request, Receiving $receiving)
    {
        $validator = Validator::make($request->all(), [
            'qty_received' => 'required|integer',
            'unit_price' => 'nullable|numeric',
            'date_received' => 'nullable|date',
            'fo_number' => 'nullable|string|max:255',
            'beginning_inventory' => 'nullable|integer',
            'ending_inventory' => 'nullable|integer',
            'details_file' => 'nullable|file|mimes:xlsx,xls,csv',
            // fields used for manual receivings
            'part_number' => 'nullable|string|max:255',
            'item_name' => 'nullable|string|max:255',
            'supplier' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $oldQty = $receiving->qty_received ?? 0;

        // handle details file replacement if uploaded
        if ($request->hasFile('details_file')) {
            // store new file
            $path = $request->file('details_file')->store('receivings', 'public');
            // attempt to delete old file if exists
            if ($receiving->details_file_path) {
                try { \Illuminate\Support\Facades\Storage::disk('public')->delete($receiving->details_file_path); } catch (\Exception $e) { /* ignore */ }
            }
            $receiving->details_file_path = $path;
        }

        $receiving->fo_number = $request->fo_number ?? $receiving->fo_number;
        $receiving->date_received = $request->date_received ?? $receiving->date_received;
        $receiving->qty_received = $request->qty_received;
        $receiving->unit_price = $request->unit_price ?? $receiving->unit_price;
        $receiving->beginning_inventory = $request->beginning_inventory ?? $receiving->beginning_inventory;
        $receiving->ending_inventory = $request->ending_inventory ?? $receiving->ending_inventory;

        // allow updating manual fields for manual receivings (when not linked to a product)
        if (is_null($receiving->product_id)) {
            if ($request->has('part_number')) $receiving->part_number = $request->part_number;
            if ($request->has('item_name')) $receiving->item_name = $request->item_name;
            if ($request->has('supplier')) $receiving->supplier = $request->supplier;
        }

        $receiving->save();

        // adjust product inventory if linked
        if ($receiving->product_id) {
            $product = Product::find($receiving->product_id);
            if ($product) {
                if (is_null($product->ending_inventory)) $product->ending_inventory = 0;
                $delta = (int)$receiving->qty_received - (int)$oldQty;
                $product->ending_inventory = $product->ending_inventory + $delta;
                $product->save();
            }
        }

        $receiving->load('product');
        return response()->json($receiving);
    }

    /**
     * Delete a receiving entry and adjust product inventory if linked.
     */
    public function destroy(Request $request, Receiving $receiving)
    {
        $qty = (int) ($receiving->qty_received ?? 0);
        $productId = $receiving->product_id;

        // delete file if exists
        if ($receiving->details_file_path) {
            try { Storage::disk('public')->delete($receiving->details_file_path); } catch (\Exception $e) { /* ignore */ }
        }

        $receiving->delete();

        // adjust product inventory if linked
        if ($productId) {
            $product = Product::find($productId);
            if ($product) {
                if (is_null($product->ending_inventory)) $product->ending_inventory = 0;
                $product->ending_inventory = max(0, $product->ending_inventory - $qty);
                $product->save();
            }
        }

        return response()->json(['deleted' => true]);
    }
}
