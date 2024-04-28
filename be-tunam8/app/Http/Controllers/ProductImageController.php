<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Logics\ProductRepository as ProductImagesLogic;
use App\Models\ProductImage;

class ProductImageController extends Controller
{
    public function updateProductImage(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'images' => 'required|array|min:1|max:4',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $productImagesLogic = new ProductImagesLogic();
        $productImagesLogic->deleteProductImage($request->product_id);
        $addImage = $productImagesLogic->addProductImage(0, $request->product_id, $request->file('images'));

        if ($addImage) {
            return response()->json(['message' => 'Image added successfully'], 200);
        } else {
            return response()->json(['message' => 'Err: Product images limit'], 500);
        }
    }

    public function getAllProductImages()
    {
        $productImages = ProductImage::all();
        return response()->json($productImages, 200);
    }
}
