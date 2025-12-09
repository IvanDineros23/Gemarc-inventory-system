<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Barryvdh\DomPDF\Facade\Pdf;

class InventoryExportController extends Controller
{
    /**
     * Export low stock items as CSV or show printable template.
     * Query params:
     *  - format=csv|print (default: csv)
     *  - threshold=int (default: 5)
     */
    public function exportLowStock(Request $request)
    {
        $format = $request->query('format', 'csv');
        $threshold = (int) $request->query('threshold', 5);

        try {
            $items = DB::table('products')
                ->select('id', 'part_number', 'inventory_id', 'name', 'brand', 'supplier', 'ending_inventory', 'unit_price')
                ->whereNotNull('ending_inventory')
                ->where('ending_inventory', '<=', $threshold)
                ->orderBy('ending_inventory', 'asc')
                ->get();

            if ($format === 'print') {
                return view('exports.low-stock-print', [
                    'items' => $items,
                    'threshold' => $threshold,
                ]);
            }

            if ($format === 'pdf') {
                // Generate PDF (server-side) and return download. PDF will exclude unit_price per request.
                $pdf = Pdf::loadView('exports.low-stock-pdf', [
                    'items' => $items,
                    'threshold' => $threshold,
                ]);

                $pdfFilename = sprintf('low_stock_items_%s_threshold_%d.pdf', date('Ymd_His'), $threshold);
                return $pdf->download($pdfFilename);
            }

            // Default: CSV download
            $filename = sprintf('low_stock_items_%s_threshold_%d.csv', date('Ymd_His'), $threshold);

            $response = new StreamedResponse(function () use ($items) {
                $out = fopen('php://output', 'w');
                // Header row
                fputcsv($out, ['ID', 'Part Number', 'Inventory ID', 'Name', 'Brand', 'Supplier', 'On Hand', 'Unit Price']);

                foreach ($items as $row) {
                    fputcsv($out, [
                        $row->id,
                        $row->part_number,
                        $row->inventory_id,
                        $row->name,
                        $row->brand,
                        $row->supplier,
                        $row->ending_inventory,
                        $row->unit_price,
                    ]);
                }

                fclose($out);
            });

            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="' . $filename . '"');

            return $response;

        } catch (\Exception $e) {
            Log::error('Export low stock failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to export low stock items: ' . $e->getMessage());
        }
    }
}
