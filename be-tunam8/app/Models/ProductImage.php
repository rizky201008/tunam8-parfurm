<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'link', 'path'];
    protected $hidden = ['created_at', 'updated_at', 'product_id', 'id'];
    protected function link(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => url($value),
        );
    }
}
