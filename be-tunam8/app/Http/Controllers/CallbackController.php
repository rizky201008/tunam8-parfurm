<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class CallbackController extends Controller
{
    public function callback(Request $request)
    {
        $data = $request->all();
        $trxId = $data['order_id'];
        $status = $data['transaction_status'];

        $transaction = Transaction::find($trxId);

        switch ($status) {
            case 'settlement':
                $transaction->update([
                    'status' => 'proccess'
                ]);
                $transaction->transactionPayment->update([
                    'status' => 'success'
                ]);
                break;

            case 'cancel':
                $transaction->update([
                    'status' => 'canceled'
                ]);
                $transaction->transactionPayment->update([
                    'status' => 'canceled'
                ]);
                break;

            default:
                # code...
                break;
        }

        Http::post('https://webhook.site/e7411339-6bdc-4814-9f7e-afb9b1478557', $transaction->transactionPayment);
        Http::post('https://webhook.site/e7411339-6bdc-4814-9f7e-afb9b1478557', $request->all());

        return response()->json(
            [
                'status' => 'success',
                'message' => 'success'
            ]
        );
    }
}
