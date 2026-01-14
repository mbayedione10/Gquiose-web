<?php

namespace App\Http\Responses;

use Illuminate\Http\Response;

class ApiResponse
{
    public static function success($data = null, $code = Response::HTTP_OK, $message = 'OK')
    {
        if ($data == null) {
            return \response()->json([
                'code' => $code,
                'message' => $message,
            ], $code);
        }

        return \response()->json([
            'code' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    public static function error($message, $code = Response::HTTP_NOT_FOUND)
    {
        return \response()->json([
            'code' => $code,
            'message' => $message,
        ]);
    }
}
