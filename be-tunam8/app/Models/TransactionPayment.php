<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'link',
        'transaction_id',
        'status',
    ];

    protected $hidden = [
        'transaction_id',
        'created_at',
        'updated_at',
        'id'
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
