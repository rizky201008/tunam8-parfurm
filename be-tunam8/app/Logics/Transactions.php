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
    protected $client;

    public function __construct(Transaction $transaction, TransactionItem $transactionItem, Product $product, Address $address, CartItem $cartItem)
    {
        $this->address = $address;
        $this->transaction = $transaction;
        $this->transactionItems = $transactionItem;
        $this->product = $product;
        $this->cartItem = $cartItem;
        $this->client = new \GuzzleHttp\Client();
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

    public function getShippingCost($address_id, $transactionItems)
    {
        $apiKey = config('app.rajaongkir_apikey');
        $origin = config('app.store_city_id');
        $address = $this->address->find($address_id);
        $itemAmount = 0;

        foreach ($transactionItems as $item) {
            $itemAmount += $item['qty'];
        }

        try {
            $response = $this->client->request('POST', "https://api.rajaongkir.com/starter/cost", [
                'headers' => [
                    'key' => $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'origin' => $origin,
                    'destination' => $address->city_id,
                    'weight' => 500 * $itemAmount, // weight in grams
                    'courier' => 'jne', // courier code
                ],
            ]);

            $data = json_decode($response->getBody()->getContents());

            // Mendapatkan layanan REG saja
            $regService = collect($data->rajaongkir->results[0]->costs)->where('service', 'REG')->first();

            if ($regService) {
                return $regService->cost[0]->value;
            } else {
                return 0;
            }
        } catch (\Exception $e) {
            return $e;
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
