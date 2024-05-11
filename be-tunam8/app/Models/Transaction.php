<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'total',
        'user_id',
        'status',
        'address_id',
        'tracking_number',
        'cost'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address() :BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function transactionPayment() : HasOne
    {
        return $this->hasOne(TransactionPayment::class);
    }
}
