<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Delivery;

class DeliveryEntryController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('name')->get();
        return view('pages.delivery-entry', compact('products'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => 'required|exists:products,id',
            'date' => 'nullable|date',
            'qty' => 'required|integer|min:1',
            'remarks' => 'nullable|string|max:1000',
        ]);

        Delivery::create([
            'product_id' => $data['product_id'],
            'date' => $data['date'] ?? now(),
            'qty' => $data['qty'],
            'remarks' => $data['remarks'] ?? null,
        ]);

        return redirect()->route('delivery.entry')->with('success', 'Delivery recorded successfully.');
    }
}
