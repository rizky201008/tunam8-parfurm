<?php

namespace App\Http\Controllers;

use App\Logics\ProductImages as ProductImagesLogic;
use App\Models\Product;
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
        foreach ($products as $product) {
            $product->tags = json_decode($product->tags);
            $product->images->map(function ($image) use ($baseUrl) {
                $image->link = $baseUrl . '/products/' . $image->link;
                return $image;
            });
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
            'images' => 'required|array|min:1|max:4',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'tags' => 'required'
        ]);

        $product = new Product;
        $product->name = $request->name;
        $product->category_id = $request->category_id;
        $product->price = $request->price;
        $product->stock = $request->stock;
        $product->description = $request->description;
        $product->tags = json_encode($request->tags);
        $product->slug = Str::slug(round(microtime(true) * 1000) . $request->name, '-');
        $product->save();

        $productLogic = new ProductImagesLogic();
        if ($product->save()) {
            $imageCount = $productLogic->countImages($request->product_id);
            $addImages = $productLogic->addProductImage($imageCount, $product->id, $request->file('images'));
        } else {
            return response()->json(
                [
                    'message' => 'Err: Cannot add product',
                ],
                500
            );
        }

        // Get base URL
        $baseUrl = config('app.url') . '/products';

        // Modify product images link with base URL
        if ($addImages) {
            $product->images = $product->with('images')->find($product->id)->images->map(function ($image) use ($baseUrl) {
                $image->link = $baseUrl . '/' . $image->link;
                return $image;
            });

            $product->tags = json_decode($product->tags);

            return response()->json(
                [
                    'message' => 'Product created',
                    'product' => [
                        $product
                    ],
                ],
                201
            );
        } else {
            return response()->json(
                [
                    'message' => 'Err: Cannot add images to product',
                ],
                500
            );
        }
    }

    public function updateProduct(Request $request)
    {
       $request->validate([
            'id' => 'required|exists:products,id',
        ]);

        $productClass = new Product();

        $product = $productClass->find($request->id);

        if ($product->name !== $request->name) {
            $validated['slug'] = Str::slug(round(microtime(true) * 1000) . $request->name, '-');
        }

        if ($request->tags !==null) {
            $request['tags'] = json_encode($request->tags);
        }

        $product->update(
            $request->all()
        );

        $productWithImages = $productClass->with(['category', 'images'])->find($request->id);

        $productWithImages->tags = json_decode($productWithImages->tags);

        $baseURL = config('app.url') . '/products';
        $productWithImages->images->map(function ($image) use ($baseURL) {
            $image->link = $baseURL . '/' . $image->link;
            return $image;
        });
        return response()->json(
            [
                'message' => 'Product updated',
                'product' => $productWithImages,
            ],
            200
        );
    }

    public function deleteProduct(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:products,id',
        ]);
        $product = Product::find($request->id);
        $product->delete();
        return response()->json(['message' => 'Product deleted']);
    }

    public function getProduct($slug)
    {
        $product = Product::with(['category', 'images'])->where('slug', $slug)->first();

        if ($product == null) {
            return response()->json([
                'message' => 'Product not found',
            ], 404);
        }

        // Adding base URL to image links
        $baseURL = config('app.url') . '/products'; // Replace 'https://example.com' with your actual base URL
        $product->images->map(function ($image) use ($baseURL) {
            $image->link = $baseURL . '/' . $image->link;
            return $image;
        });

        return response()->json($product);
    }
}
