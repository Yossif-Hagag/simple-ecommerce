<?php

namespace App\Http\Controllers\Api;

trait ApiResponseTrait
{
    public function apiResponse($data = null, $status = null, $message = null)
    {
        $array = [
            'data' => $data,
            'status' => $status,
            'message' => $message,
        ];

        return response($array, $status);
    }
}