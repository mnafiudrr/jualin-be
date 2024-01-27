<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait HttpResponses
{
    /**
     * Send a success response
     *
     * @param string $message
     * @param mixed $data
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function successResponse($data = null, $message = 'success', $code = 200)
    {
        return response()->json([
            'status' => true,
            'message' => $message,
            'data' => $data
        ], $code);
    }

    /**
     * Send an error response
     *
     * @param string $message
     * @param mixed $data
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public function errorResponse($error, $message = 'failed', $code = 400)
    {
        Log::error([
            'message' => $message,
            'errors' => $error,
        ]);

        return response()->json([
            'status' => false,
            'message' => $message,
            'errors' => $error,
        ], $code);
    }
}