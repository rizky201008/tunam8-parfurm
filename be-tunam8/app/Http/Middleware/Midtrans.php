<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Midtrans
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $orderId = $request['order_id'];
        $statusCode = $request['status_code'];
        $grossAmount = $request['gross_amount'];
        $serverKey  = config('app.midtrans_server_key');

        $stringToHash = $orderId . $statusCode . $grossAmount . $serverKey;

        $hash = hash('sha512', $stringToHash);

        if ($hash !== $request['signature_key']) {
            return response()->json([
                'status' => 'error',
                'message' => 'invalid signature, unauthenticated! '.$hash.' needed'
            ], 401);
        }
        return $next($request);
    }
}
