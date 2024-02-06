<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function allProducts()
    {
        $products = Product::paginate(10);
        foreach ($products as $product) {
            $product->images = json_decode($product->images);
        }

        return response()->json($products);
    }

    public function createProduct(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'description' => 'required',
            'images' => 'required|array',
        ]);

        $product = new Product;
        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->description = $request->description;
        $product->images = json_encode($request->images);
        $product->slug = Str::slug($request->name, '-');
        $product->save();

        return response()->json(
            [
                'message' => 'Product created',
                'product' => $product,
            ],
            201
        );
    }

    public function updateProduct(Request $request)
    {
        $product = Product::find($request->id);

        $product->update($request->all());

        return response()->json($product);
    }

    public function deleteProduct(Request $request)
    {
        $product = Product::find($request->id);
        $product->delete();
        return response()->json(['message' => 'Product deleted']);
    }

    public function getProduct($slug)
    {
        $product = Product::where('slug', $slug)->first();
        $product->images = json_decode($product->images);
        return response()->json($product);
    }
}
