<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;





class InventoryController extends Controller
{
    public function index()
    {
        $products = Product::latest()->paginate(10);

        return view('admin.products', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'required|string|max:255|unique:products',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'nullable|in:1,0',
        ]);

        

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('uploads/products', 'public');
        }


        $product = Product::create([
            'name' => $request->name,
            'sku' => $request->sku,
            'status' => $request->status ? 1 : 1,
            'image' => $imagePath ?? null,
        ]);

        return redirect()->route('admin.products')->with('success', 'Product created successfully.');
    }
}
