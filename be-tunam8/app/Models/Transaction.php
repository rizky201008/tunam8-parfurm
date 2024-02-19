<?php

namespace App\Models;

use App\Models\User;
use App\Models\Address;
use App\Models\TransactionItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'total',
        'user_id',
        'status',
        'address_id',
        'tracking_number',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function transactionItems(){
        return $this->hasMany(TransactionItem::class);
    }
}
