<?php

namespace App\Repository;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class ProductRepository
{
    public function countImages($productId)
    {
        $product = Product::with('images')->find($productId);

        if ($product !== null && $product->images !== null) {
            return count($product->images);
        }
        return 0;
    }

    public function addProductImage($imagesCount, $productId, $images)
    {
        $files = [];
        if ($imagesCount <= 4 && $imagesCount + count($images) <= 4) {
            foreach ($images as $file) {
                $hashedName = $file->hashName();
                $file->store('product');
                $path = Storage::path('product/' . $hashedName);
                $url ='storage/product/' . $hashedName;
                $files[] = [
                    'product_id' => $productId,
                    'link' => $url,
                    'path' => $path
                ];
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
            $imagePath = $image->path;
            $image->delete();
            File::delete($imagePath);
        }

        return true;
    }
}
