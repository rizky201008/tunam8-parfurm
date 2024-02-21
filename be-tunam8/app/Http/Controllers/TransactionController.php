<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionItem;
use App\Logics\Transactions as TransactionLogic;

class TransactionController extends Controller
{
    protected $product;
    protected $transactionLogic;
    protected $transaction;
    protected $transactionItems;
    protected $address;
    protected $cartItem;

    public function __construct()
    {
        $this->product = new Product();
        $this->transaction = new Transaction();
        $this->transactionItems = new TransactionItem();
        $this->address = new Address();
        $this->cartItem = new CartItem();
        $this->transactionLogic = new TransactionLogic($this->transaction, $this->transactionItems, $this->product, $this->address, $this->cartItem);
    }

    public function allTransactions()
    {
        return response()->json($this->transaction->all());
    }

    public function getTransactions(Request $request)
    {
        return response()->json($request->user()->transactions->get());
    }

    public function createTransaction(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'products' => 'required|array',
        ]);

        $validateQty = $this->transactionLogic->validateStock($request->products);
        $validateAddressOwner = $this->transactionLogic->validateAddressOwner($request->address_id, $request->user()->id);

        $address = $this->address->find($request->address_id);

        if ($validateQty['error']) {
            return response()->json(
                [
                    'message' => $validateQty['message']
                ],
                400
            );
        }
        if ($validateAddressOwner['error']) {
            return response()->json(
                [
                    'message' => $validateAddressOwner['message']
                ],
                400
            );
        }

        $this->transactionLogic->decreaseStock($request->products);
        $this->transactionLogic->deleteCartItem($request->products, $request->user()->id);
        $cost = $this->transactionLogic->getShippingCost($address, $request->products);

        $total = $this->transactionLogic->getTotal($request->products);

        $transaction = [
            'total' => $total + $cost,
            'user_id' => $request->user()->id,
            'status' => 'unpaid',
            'address_id' => $request->address_id,
            'cost' => $cost,
        ];

        $insertTransaction = $this->transactionLogic->insertTransaction($transaction, $request->products);

        if ($insertTransaction['error']) {
            return response()->json(
                [
                    'message' => $insertTransaction['message']
                ],
                400
            );
        }

        return response()->json([
            'message' => 'Transaction created'
        ], 201);
    }

    public function getShippingCost(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'products' => 'required|array',
        ]);

        $cost = $this->transactionLogic->getShippingCost($request->address_id, $request->products);

        return response()->json([
            'cost' => $cost
        ], 200);
    }

    public function deleteTransaction(Request $request)
    {
        $transaction = Transaction::find($request->id);
        $transaction->delete();
        return response()->json(['message' => 'Transaction deleted']);
    }

    public function calculateOngkir(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'products' => 'required|array',
        ]);
    }
}
