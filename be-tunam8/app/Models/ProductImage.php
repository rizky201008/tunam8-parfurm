<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    use HasFactory;
    protected $fillable = ['product_id', 'link'];
    protected $hidden = ['created_at', 'updated_at', 'product_id', 'id'];
}
