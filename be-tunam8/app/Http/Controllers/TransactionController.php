<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Address;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\TransactionItem;
use App\Models\TransactionPayment;
use Illuminate\Support\Facades\Validator;
use App\Logics\Transactions as TransactionLogic;

class TransactionController extends Controller
{
    protected $product;
    protected $transactionLogic;
    protected $transaction;
    protected $transactionItems;
    protected $address;
    protected $cartItem;
    protected $transactionPayment;

    public function __construct()
    {
        $this->product = new Product();
        $this->transaction = new Transaction();
        $this->transactionItems = new TransactionItem();
        $this->address = new Address();
        $this->cartItem = new CartItem();
        $this->transactionPayment = new TransactionPayment();
        $this->transactionLogic = new TransactionLogic($this->transaction, $this->transactionItems, $this->product, $this->address, $this->cartItem, $this->transactionPayment);
    }

    public function allTransactions(Request $request)
    {
        $status = $request->query('status');
        if ($status !== null) {
            return response()->json($this->transaction->with(['transactionItems', 'transactionPayment', 'user'])->where('status', $status)->get());
        }
        return response()->json($this->transaction->with(['transactionItems', 'transactionPayment', 'user'])->get());
    }

    public function getTransactions(Request $request)
    {
        $status = $request->query('status');

        if ($status !== null) {
            return response()->json($this->transaction->with(['transactionItems', 'transactionPayment'])->where('status', $status)->where('user_id', $request->user()->id)->get());
        }
        return response()->json($this->transaction->with(['transactionItems', 'transactionPayment'])->where('user_id', $request->user()->id)->get());
    }

    public function getTransaction(Request $request, $transactionId)
    {
        $validator = Validator::make(['transaction_id' => $transactionId], [
            'transaction_id' => 'required|exists:transactions,id'
        ]);
        if ($validator->fails()) {
            throw new Exception($validator->errors()->first());
        }
        if ($request->user()->role != 'admin') {
            $transaction = $this->transaction->with(['transactionItems', 'transactionPayment', 'address'])->where('id', $transactionId)->where('user_id', $request->user()->id)->first();
        } else {
            $transaction = $this->transaction->with(['transactionItems', 'transactionPayment', 'address'])->where('id', $transactionId)->first();
        }

        $transaction->transactionItems->map(function ($item) {
            $item->product = $this->product->find($item->product_id);
            $item->product->image = $item->product->images[0]->link;
            unset($item->product->images); // Remove images field
            return $item;
        });

        return response()->json(
            $transaction
        );
    }

    public function createTransaction(Request $request)
    {
        $request->validate([
            'address_id' => 'required|exists:addresses,id',
            'products' => 'required|array',
        ]);

        $this->transactionLogic->validateStock($request->products);

        $address = $this->address->find($request->address_id);

        $this->transactionLogic->decreaseStock($request->products);
        $this->transactionLogic->deleteCartItem($request->products, $request->user()->id);
        $cost = $this->transactionLogic->getShippingCost($address->city_id, $request->products);

        if ($cost['error']) {
            throw new Exception($cost['message']);
        }

        $total = $this->transactionLogic->getTotal($request->products);

        $transaction = [
            'total' => $total + $cost['cost'],
            'user_id' => $request->user()->id,
            'status' => 'unpaid',
            'address_id' => $request->address_id,
            'cost' => $cost['cost'],
        ];

        $insertTransaction = $this->transactionLogic->insertTransaction($transaction, $request->products);

        $paymentLink = $this->transactionLogic->createMidtransSnapLink($total + $cost['cost'], $insertTransaction['transaction_id'] . rand(1000, 9999));


        if ($insertTransaction['error']) {
            throw new Exception($insertTransaction['message']);
        }

        if ($paymentLink['error']) {
            throw new Exception($paymentLink['message']);
        }

        return response()->json([
            'message' => 'Transaction created',
            'link' => $paymentLink['payment_url'],
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
            'cost' => $cost['cost']
        ], 200);
    }

    public function searchTransactions(Request $request)
    {
//        $results = null;
        $results = Transaction::with(['transactionItems', 'user'])->where('id', 'like', '%' . $request->query('q') . '%')->orWhereHas('user', function ($query) use ($request) {
            $query->where('name', 'like', '%' . $request->query('q') . '%');
        })->get();

        return response()->json($results);
    }

    public function deleteTransaction(Request $request)
    {
        $transaction = Transaction::find($request->id);
        $transaction->delete();
        return response()->json(['message' => 'Transaction deleted']);
    }

    public function updateTransaction(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:transactions,id',
        ]);
        $this->transaction->find($request->id)->update($request->all());
        return response()->json(['message' => 'Transaction updated']);
    }
}
