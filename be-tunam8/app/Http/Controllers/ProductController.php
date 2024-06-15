<?php

namespace App\Http\Controllers;

use App\Repository\ProductRepository;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function allProducts()
    {
        $products = Product::with(['category', 'images'])->get();

        return response()->json(['data' => $products]);
    }

    public function getPersonalizedProducts(Request $request)
    {
        $user = $request->user();
        $tags = $user->personal;
        $explodedTags = explode(',', $tags->tags);
        $filteredProduct = [];

        $jenis_kelamin = $user->jenis_kelamin;
        if ($jenis_kelamin == null) {
            $query = Product::with(['category', 'images'])->where('jenis_kelamin', 'u');
        } else {
            $query = Product::with(['category', 'images'])->where('jenis_kelamin', $jenis_kelamin)->orWhere('jenis_kelamin', 'u');
        }

        if (!empty($explodedTags)) {
            foreach ($explodedTags as $tag) {
                $tag = Str::replace(' ', '', $tag);
                $products = $query->where('tags', 'LIKE', "%$tag%")->first();
                if ($products !== null) {
                    $filteredProduct[] = $products;
                }
            }
        } else {
            $products = $query->get();
            if ($products->isNotEmpty()) {
                $filteredProduct = $products->toArray();
            }
        }

        return response()->json(array_slice($filteredProduct, 0, 5));
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
        $product->tags = $request->tags;
        $product->slug = Str::slug(round(microtime(true) * 1000) . $request->name, '-');
        $product->save();

        $productRepository = new ProductRepository();
        if ($product->save()) {
            $imageCount = $productRepository->countImages($request->product_id);
            $addImages = $productRepository->addProductImage($imageCount, $product->id, $request->file('images'));
        } else {
            return response()->json(
                [
                    'message' => 'Err: Cannot add product',
                ],
                500
            );
        }

        // Modify product images link with base URL
        if ($addImages) {

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

        $product->update(
            $request->all()
        );

        $productWithImages = $productClass->with(['category', 'images'])->find($request->id);

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

        return response()->json($product);
    }

    public function searchProducts(Request $request)
    {
        $name = $request->query('query');
        $category = $request->query('category');

        if ($name == null && $category == null) {
            $products = Product::with(['category', 'images'])->get();
        } elseif ($name == null && $category !== null) {
            $products = Product::with(['category', 'images'])->where('category_id', $category)->get();
        } elseif ($name !== null && $category == null) {
            $products = Product::with(['category', 'images'])->where('name', 'LIKE', "%$name%")->get();
        } else {
            $products = Product::with(['category', 'images'])->where('name', 'LIKE', "%$name%")->where('category_id', $category)->get();
        }

        return response()->json($products);
    }
}
