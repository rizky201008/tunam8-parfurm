<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function allProducts()
    {
        $products = Product::with(['category', 'images'])->paginate(10);

        // Get base URL
        $baseUrl = config('app.url');

        // Modify each product's images link with base URL
        $products->map(function ($product) use ($baseUrl) {
            $product->images->map(function ($image) use ($baseUrl) {
                $image->link = $baseUrl . '/products/' . $image->link;
                return $image;
            });
            return $product;
        });

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
            'images' => 'required',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $product = new Product;
        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->description = $request->description;
        $product->slug = Str::slug(round(microtime(true) * 1000) . $request->name, '-');
        $product->save();
        $files = [];
        if ($request->hasfile('images')) {
            foreach ($request->file('images') as $file) {
                if ($file->isValid()) {
                    $filename = round(microtime(true) * 1000) . '-' . str_replace(' ', '-', $file->getClientOriginalName());
                    $file->move(public_path('products'), $filename);
                    $files[] = [
                        'product_id' => $product->id,
                        'link' =>  $filename,
                    ];
                }
                ProductImage::insert($files);
            }
        }

        // Get base URL
        $baseUrl = config('app.url');

        // Modify product images link with base URL
        $productImages = Product::where('id', $product->id)->with('images')->first()->images->map(function ($image) use ($baseUrl) {
            $image->link = $baseUrl . '/products/' . $image->link;
            return $image;
        });

        $product->images = $productImages;

        return response()->json(
            [
                'message' => 'Product created',
                'product' => [
                    $product
                ],
            ],
            201
        );
    }

    public function updateProduct(Request $request)
    {
        $product = Product::find($request->id);

        if ($request['name'] !== null) {
            $request['slug'] = Str::slug($request['name'], '-');
        }

        $product->update($request->all());

        $product->images = json_decode($product->images);

        return response()->json(
            [
                'message' => 'Product updated',
                'product' => $product,
            ],
            200
        );
    }

    public function deleteProduct(Request $request)
    {
        $product = Product::find($request->id);
        $product->delete();
        return response()->json(['message' => 'Product deleted']);
    }

    public function getProduct($slug)
    {
        $product = Product::with(['category', 'images'])->where('slug', $slug)->first();

        $baseUrl = config('app.url');

        $product->images->map(function ($image) use ($baseUrl) {
            $image->link = $baseUrl . '/products/' . $image->link;
            return $image;
        });

        return response()->json($product);
    }
}