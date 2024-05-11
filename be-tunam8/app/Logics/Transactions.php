<?php

namespace App\Logics;

use Exception;
use App\Models\Address;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\TransactionPayment;
use Illuminate\Support\Facades\Validator;

class Transactions
{
    protected $product;
    protected $transaction;
    protected $transactionItems;
    protected $address;
    protected $cartItem;
    protected $client;
    protected $transactionPayment;

    public function __construct(Transaction $transaction, TransactionItem $transactionItem, Product $product, Address $address, CartItem $cartItem, TransactionPayment $transactionPayment)
    {
        $this->address = $address;
        $this->transaction = $transaction;
        $this->transactionItems = $transactionItem;
        $this->product = $product;
        $this->cartItem = $cartItem;
        $this->client = new \GuzzleHttp\Client();
        $this->transactionPayment = $transactionPayment;
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
                throw new Exception("Stock $produk->name tidak mencukupi");
            }
        }
    }

    public function validateAddressOwner($addressId, $userId)
    {
        $address = $this->address->find($addressId);
        if ($address->user_id !== $userId) {
            throw new Exception('Address not found');
        }
    }

    public function decreaseStock($products)
    {
        foreach ($products as $product) {
            $produk = $this->product->find($product['id']);
            $produk->stock = $produk->stock - $product['qty'];
            $produk->save();
        }
    }

    public function deleteCartItem($products, $userId)
    {
        foreach ($products as $product) {
            $this->cartItem->where('product_id', $product['id'])->where('user_id', $userId)->delete();
        }
    }

    public function getShippingCost($city_id, $transactionItems)
    {
        $apiKey = config('app.rajaongkir_apikey');
        $origin = config('app.store_city_id');
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
                    'destination' => $city_id,
                    'weight' => 500 * $itemAmount, // weight in grams
                    'courier' => 'jne', // courier code
                ],
            ]);

            $data = json_decode($response->getBody()->getContents());

            // Mendapatkan layanan REG saja
            $regService = collect($data->rajaongkir->results[0]->costs)->where('service', 'REG')->first();

            if ($regService) {
                return [
                    'error' => false,
                    'message' => '',
                    'cost' => $regService->cost[0]->value
                ];
            } else {
                return [
                    'error' => false,
                    'message' => '',
                    'cost' => 0
                ];
            }
        } catch (\Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }


    public function insertTransaction($transaction, $transactionItems)
    {
        $trx = $this->transaction->create([
            'total' => $transaction['total'],
            'user_id' => $transaction['user_id'],
            'status' => $transaction['status'],
            'address_id' => $transaction['address_id'],
            'cost' => $transaction['cost'],
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
                'message' => 'Failed to insert transaction items',
                'transaction_id' => null
            ];
        }

        return [
            'error' => false,
            'message' => '',
            'transaction_id' => $trx->id
        ];
    }

    public function createMidtransSnapLink($amount, $trxId)
    {
        // Set your Merchant Server Key
        \Midtrans\Config::$serverKey = config('app.midtrans_server_key');
        // Set to Development/Sandbox Environment (default). Set to true for Production Environment (accept real transaction).
        \Midtrans\Config::$isProduction = false;
        // Set sanitization on (default)
        \Midtrans\Config::$isSanitized = true;
        // Set 3DS transaction for credit card to true
        \Midtrans\Config::$is3ds = true;
        \Midtrans\Config::$appendNotifUrl = config('app.midtrans_callback_url');
        $params = array(
            'transaction_details' => array(
                'order_id' => $trxId . rand(1, 99999),
                'gross_amount' => (int)$amount,
            )
        );

        try {
            // Get Snap Payment Page URL
            $paymentUrl = \Midtrans\Snap::createTransaction($params)->redirect_url;

            $this->transactionPayment->create([
                'link' => $paymentUrl,
                'transaction_id' => $trxId,
                'status' => 'unpaid'
            ]);

            // Redirect to Snap Payment Page
            return [
                'error' => false,
                'message' => '',
                'payment_url' => $paymentUrl
            ];
        } catch (Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }
}
