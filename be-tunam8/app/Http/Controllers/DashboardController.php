<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userCount = User::count();
        $transactions = Transaction::where('status', 'received');
        $products = Product::count();
        $transactionsCount = $transactions->count();
        $omzetTotal = $transactions->sum('total');
        $currentYear = Carbon::now()->year;
        $omzetBulan = Transaction::selectRaw('COALESCE(SUM(total), 0) as total')
            ->where('status', 'received')
            ->whereYear('created_at', $currentYear)
            ->groupByRaw('MONTH(created_at)')
            ->orderByRaw('MONTH(created_at)')
            ->pluck('total')
            ->toArray();

        // Memastikan array memiliki panjang 12 bulan dan mengganti nilai yang tidak ada dengan 0
        $omzetBulan = array_pad($omzetBulan, 12, 0);

        return response()->json(
            [
                'user_total' => $userCount,
                'omzet_all' => $omzetTotal,
                'omzet_bulan' => $omzetBulan,
                'transaction_total' => $transactionsCount,
                'product_total' => $products
            ]
        );
    }
}
