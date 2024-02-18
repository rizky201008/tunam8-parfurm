<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'province',
        'province_id',
        'city',
        'city_id',
        'postal_code',
        'address_detail',
        'phone_number',
        'receiver',
        'label',
        'is_primary',
        'user_id'
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'user_id'
    ];
}
