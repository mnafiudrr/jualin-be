<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

// must have shop_id in header
class AddShopIdToRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!$shopIdFromHeader = $request->header('shop_id'))
            return response()->json([
                'message' => 'header Shop id is required',
                'errors' => [
                    'shop_id' => ['header Shop id is required'],
                ],
            ], 422);

        if (!auth()->user()->shops->where('id', $shopIdFromHeader)->first())
            return response()->json([
                'message' => 'shop is invalid',
                'errors' => [
                    'shop_id' => ['shop_id is invalid'],
                ],
            ], 422);

        session(['shop_id' => $shopIdFromHeader]);

        return $next($request);
    }
}
