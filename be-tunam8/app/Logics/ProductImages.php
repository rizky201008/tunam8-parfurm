<?php

namespace App\Logics;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\File;

class ProductImages
{
    public function countImages($productId)
    {
        $product = Product::with('images')->find($productId);

        if ($product !== null && $product->images !== null) {
            return count($product->images);
        }
        return  0;
    }

    public function addProductImage($imagesCount, $productId, $images)
    {
        $files = [];
        if ($imagesCount <= 4 && $imagesCount + count($images) <= 4) {
            foreach ($images as $file) {
                if ($file->isValid()) {
                    $filename = round(microtime(true) * 1000) . '-' . str_replace(' ', '-', $file->getClientOriginalName());
                    $file->move(public_path('products'), $filename);
                    $files[] = [
                        'product_id' => $productId,
                        'link' =>  $filename,
                    ];
                }
            }
            ProductImage::insert($files);
            return true;
        }

        return false;
    }

    public function deleteProductImage($product_id)
    {
        $productImage = ProductImage::where('product_id', $product_id)->get();
        foreach ($productImage as $image) {
            $imagePath = public_path('products') . '/' . $image->link;
            $image->delete();
            File::delete($imagePath);
        }
        
        return true;
    }
}
