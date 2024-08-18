<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function getProducts() {
        $products = Product::orderBy('id', 'DESC')->get();
        return response()->json($products);
    }

    public function createProduct(Request $request) {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $product = new Product();
        $product->name = $validatedData['name'];
        $product->price = $validatedData['price'];
        $product->description = $request->input('description', '');

        try {
            $product->save();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Product could not be saved'], 500);
        }

        return response()->json($product, 201);
    }
}
