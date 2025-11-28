<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Delivery;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class DeliveryEntryController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('name')->get();
        return view('pages.delivery-entry', compact('products'));
    }

    public function store(Request $request)
    {
        // Expect a header and an items[] array (cart)
        $header = $request->validate([
            'customer' => 'nullable|string|max:255',
            'dr_number' => 'nullable|string|max:255',
            'dr_date' => 'nullable|date',
            'intended_to' => 'nullable|string|max:255',
        ]);

        $items = $request->input('items', []);
        if (!is_array($items) || count($items) === 0) {
            return redirect()->route('delivery.entry')->withErrors(['items' => 'Please add at least one item to the delivery cart.']);
        }

        $createdCount = 0;
        foreach ($items as $idx => $item) {
            $v = validator($item, [
                'product_id' => 'required|exists:products,id',
                'part_number' => 'nullable|string|max:255',
                'item_name' => 'nullable|string|max:255',
                'item_description' => 'nullable|string',
                'date' => 'nullable|date',
                'qty' => 'required|integer|min:1',
                'unit_cost' => 'nullable|numeric',
                'unit' => 'nullable|string|max:50',
                'currency' => 'nullable|string|max:10',
            ]);

            if ($v->fails()) {
                return redirect()->route('delivery.entry')->withErrors($v)->withInput();
            }

            $payload = array_merge($header, $v->validated());
            // ensure date key
            $payload['date'] = $payload['date'] ?? now();

            $delivery = Delivery::create([
                'product_id' => $payload['product_id'],
                'date' => $payload['date'],
                'qty' => $payload['qty'],
                'remarks' => $request->input('remarks'),
                'dr_number' => $payload['dr_number'] ?? null,
                'customer' => $payload['customer'] ?? null,
                'dr_date' => $payload['dr_date'] ?? null,
                'part_number' => $payload['part_number'] ?? null,
                'item_name' => $payload['item_name'] ?? null,
                'item_description' => $payload['item_description'] ?? null,
                'unit_cost' => isset($payload['unit_cost']) ? $payload['unit_cost'] : null,
                'unit' => $payload['unit'] ?? null,
                'currency' => $payload['currency'] ?? null,
                'intended_to' => $payload['intended_to'] ?? null,
            ]);

            // adjust product inventory: decrement ending_inventory by qty
            $product = \App\Models\Product::find($payload['product_id']);
            if ($product) {
                $product->ending_inventory = max(0, (int)$product->ending_inventory - (int)$payload['qty']);
                // recalc total if unit_price exists
                if ($product->unit_price !== null) {
                    $product->total = $product->ending_inventory * $product->unit_price;
                }
                $product->save();
            }

            $createdCount++;
        }

        if ($createdCount > 0) {
            // Redirect to printable DR by dr_number if provided
            if (!empty($header['dr_number'])) {
                return redirect()->route('delivery.print', $header['dr_number'])->with('success', 'Delivery posted and inventory updated.');
            }
            return redirect()->route('delivery.entry')->with('success', 'Delivery posted and inventory updated.');
        }

        return redirect()->route('delivery.entry')->withErrors(['items' => 'No deliveries were created.']);
    }

    public function print($drNumber)
    {
        $rows = Delivery::where('dr_number', $drNumber)->orderBy('id')->get();
        if ($rows->isEmpty()) {
            return redirect()->route('delivery.entry')->withErrors(['notfound' => 'DR not found for printing.']);
        }

        return view('pages.delivery-printable', ['rows' => $rows, 'dr_number' => $drNumber]);
    }

    public function printBySampleId($id)
    {
        $sample = Delivery::find($id);
        if (!$sample) {
            return redirect()->route('delivery.entry')->withErrors(['notfound' => 'Sample DR not found for printing.']);
        }

        if ($sample->dr_number) {
            return $this->print($sample->dr_number);
        }

        // get group that matches customer and date (same logic as detailsBySampleId)
        $query = Delivery::whereNull('dr_number')
            ->where(function($q) use ($sample) {
                if ($sample->customer) {
                    $q->where('customer', $sample->customer);
                }
                if ($sample->dr_date) {
                    $q->whereDate('dr_date', $sample->dr_date);
                } else {
                    $q->whereDate('date', $sample->date);
                }
            });

        $rows = $query->orderBy('id')->get();
        if ($rows->isEmpty()) {
            return redirect()->route('delivery.entry')->withErrors(['notfound' => 'No deliveries found for printing.']);
        }

        // Use a generated dr_number label like 'DR:sample-{id}' for printable header
        $label = 'sample-' . $id;
        return view('pages.delivery-printable', ['rows' => $rows, 'dr_number' => $label]);
    }

    public function printPdf($identifier)
    {
        // identifier may be a dr_number or sample-<id>
        if (str_starts_with($identifier, 'sample-')) {
            $id = intval(substr($identifier, strlen('sample-')));
            $sample = Delivery::find($id);
            if (!$sample) {
                return redirect()->route('delivery.entry')->withErrors(['notfound' => 'Sample not found for PDF.']);
            }

            if ($sample->dr_number) {
                $rows = Delivery::where('dr_number', $sample->dr_number)->orderBy('id')->get();
                $label = $sample->dr_number;
            } else {
                $query = Delivery::whereNull('dr_number')->where(function($q) use ($sample) {
                    if ($sample->customer) { $q->where('customer', $sample->customer); }
                    if ($sample->dr_date) { $q->whereDate('dr_date', $sample->dr_date); } else { $q->whereDate('date', $sample->date); }
                });
                $rows = $query->orderBy('id')->get();
                $label = 'sample-' . $id;
            }
        } else {
            $rows = Delivery::where('dr_number', $identifier)->orderBy('id')->get();
            $label = $identifier;
        }

        if ($rows->isEmpty()) {
            return redirect()->route('delivery.entry')->withErrors(['notfound' => 'No deliveries found for PDF.']);
        }

        $pdf = PDF::loadView('pages.delivery-printable', ['rows' => $rows, 'dr_number' => $label]);

        // Build filename: DR_{drNumber}_{customer}_{date}.pdf
        $firstRow = $rows->first();
        $customer = $firstRow->customer ?? '';
        // determine date to use: prefer dr_date then date then now
        $dateUsed = null;
        if (!empty($firstRow->dr_date)) {
            $dateUsed = \Carbon\Carbon::parse($firstRow->dr_date)->format('Ymd');
        } elseif (!empty($firstRow->date)) {
            $dateUsed = \Carbon\Carbon::parse($firstRow->date)->format('Ymd');
        } else {
            $dateUsed = now()->format('Ymd');
        }

        // sanitize parts for filename
        $sanitize = function($s) {
            $s = (string)$s;
            $s = trim($s);
            $s = str_replace(' ', '_', $s);
            // remove any character that's not letter, number, underscore or dash
            return preg_replace('/[^A-Za-z0-9_\-]/', '', $s);
        };

        $safeLabel = $sanitize($label);
        $safeCustomer = $sanitize($customer ?: 'customer');

        $filename = sprintf('DR_%s_%s_%s.pdf', $safeLabel, $safeCustomer, $dateUsed);
        return $pdf->download($filename);
    }
}
