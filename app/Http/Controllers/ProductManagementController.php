<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Product;

class ProductManagementController extends Controller
{
    public function index()
    {
        $products = \App\Models\Product::latest()->get();
        return view('pages.product-management', compact('products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'part_number' => 'nullable|string|max:255',
            'inventory_id' => 'nullable|string|max:255',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'supplier' => 'nullable|string|max:255',
            'fo_number' => 'nullable|string|max:255',
            'date_received' => 'nullable|date',
            'qty_received' => 'nullable|integer',
            'unit_price' => 'nullable|numeric',
            'beginning_inventory' => 'nullable|integer',
            'ending_inventory' => 'nullable|integer',
            'total' => 'nullable|numeric',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $validated;

        // Handle the uploaded image file explicitly and safely
        try {
            $file = $request->file('image');
            if ($file && $file->isValid()) {
                // store on the public disk so asset('storage/...') works
                $path = $file->store('product_images', 'public');
                $data['image_path'] = $path;
            }
        } catch (\Exception $e) {
            Log::error('Product image upload failed', ['error' => $e->getMessage(), 'user_id' => auth()->id() ?? null]);
            return redirect()->back()->withInput()->withErrors(['image' => 'Failed to save uploaded image.']);
        }

        // Ensure we do not attempt to save the UploadedFile instance itself
        if (isset($data['image'])) {
            unset($data['image']);
        }

        \App\Models\Product::create($data);

        return redirect()->route('product.management')->with('success', 'Product added successfully!');
    }

    public function edit(Product $product)
    {
        return view('pages.product-edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'part_number' => 'nullable|string|max:255',
            'inventory_id' => 'nullable|string|max:255',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'supplier' => 'nullable|string|max:255',
            'fo_number' => 'nullable|string|max:255',
            'date_received' => 'nullable|date',
            'qty_received' => 'nullable|integer',
            'unit_price' => 'nullable|numeric',
            'beginning_inventory' => 'nullable|integer',
            'ending_inventory' => 'nullable|integer',
            'total' => 'nullable|numeric',
            'image' => 'nullable|image|max:2048',
        ]);

        $data = $validated;

        // Handle new image upload: store and optionally delete old file
        try {
            $file = $request->file('image');
            if ($file && $file->isValid()) {
                $path = $file->store('product_images', 'public');
                // delete old image if present
                if ($product->image_path) {
                    Storage::disk('public')->delete($product->image_path);
                }
                $data['image_path'] = $path;
            }
        } catch (\Exception $e) {
            Log::error('Product image upload failed during update', ['error' => $e->getMessage(), 'product_id' => $product->id]);
            return redirect()->back()->withInput()->withErrors(['image' => 'Failed to save uploaded image.']);
        }

        if (isset($data['image'])) {
            unset($data['image']);
        }

        $product->update($data);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($product->fresh());
        }

        return redirect()->route('product.management')->with('success', 'Product updated successfully!');
    }

    public function destroy(Request $request, Product $product)
    {
        // remove image from storage if exists
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['status' => 'ok']);
        }

        return redirect()->route('product.management')->with('success', 'Product deleted.');
    }
}
