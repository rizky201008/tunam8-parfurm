<?php

namespace App\Logics;

use App\Models\Address;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\TransactionItem;
use Illuminate\Support\Facades\Validator;

class Transactions
{
    protected $product;
    protected $transaction;
    protected $transactionItems;
    protected $address;
    protected $cartItem;

    public function __construct(Transaction $transaction, TransactionItem $transactionItem, Product $product, Address $address, CartItem $cartItem)
    {
        $this->address = $address;
        $this->transaction = $transaction;
        $this->transactionItems = $transactionItem;
        $this->product = $product;
        $this->cartItem = $cartItem;
    }
    public function getTotal($products)
    {
        $total = 0;

        foreach ($products as $product) {
            $produk = $this->product->find($product['id']);

            $total += $produk->price * $product['qty'];
        }

        return $total;
    }

    public function validateStock($products)
    {
        foreach ($products as $product) {
            $produk = $this->product->find($product['id']);

            $stock = $produk->stock + 1;

            $validator = Validator::make(['qty' => $product['qty']], [
                'qty' => "required|lt:$stock"
            ]);

            if ($validator->fails()) {
                return [
                    'message' => $validator->messages()->first(),
                    'error' => true
                ];
            }

            return [
                'message' => "",
                'error' => false
            ];
        }
    }

    public function validateAddressOwner($addressId, $userId)
    {
        $address = $this->address->find($addressId);
        if ($address->user_id !== $userId) {
            return [
                'error' => true,
                'message' => "PLease use your own address to order"
            ];
        }

        return [
            'error' => false,
            'message' => ''
        ];
    }

    public function decreaseStock($products)
    {
        foreach ($products as $product) {
            $produk = $this->product->find($product['id']);
            $produk->stock->decrement($product['qty']);
            $produk->save();
        }
    }

    public function deleteCartItem($products, $userId)
    {
        foreach ($products as $product) {
            $this->cartItem->where('product_id', $product['id'])->where('user_id', $userId)->delete();
        }
    }

    public function insertTransaction($transaction, $transactionItems)
    {
        $trx = $this->transaction->create([
            'total' => $transaction['total'],
            'user_id' => $transaction['user_id'],
            'status' => $transaction['status'],
            'address_id' => $transaction['address_id'],
        ]);

        if (!$transaction) {
            return [
                'error' => true,
                'message' => 'Failed to insert transaction'
            ];
        }

        $newTransactionItems = [];
        foreach ($transactionItems as $product) {
            $produk = $this->product->find($product['id']);
            $newTransactionItems[] = [
                'transaction_id' => $trx->id,
                'product_id' => $product['id'],
                'price' => $produk->price * $product['qty'],
                'qty' => $product['qty']
            ];
        }

        $insertTransactionItems = $this->transactionItems->insert($newTransactionItems);

        if (!$insertTransactionItems) {
            return [
                'error' => true,
                'message' => 'Failed to insert transaction items'
            ];
        }

        return [
            'error' => false,
            'message' => ''
        ];
    }
}
