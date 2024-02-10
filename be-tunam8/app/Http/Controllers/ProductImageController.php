<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Logics\ProductImages as ProductImagesLogic;

class ProductImageController extends Controller
{
    public function addProductImage(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|integer',
            'images' => 'required',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $productImagesLogic = new ProductImagesLogic();
        $imageCount = $productImagesLogic->countImages($validated['product_id']);
        $addImage = $productImagesLogic->addProductImage($imageCount, $validated['product_id'], $request->file('images'));

        if ($addImage) {
            return response()->json(['message' => 'Image added successfully'], 200);
        } else {
            return response()->json(['message' => 'Err: Product images limit'], 500);
        }
    }

    public function deleteProductImage(Request $request)
    {
    }
}
