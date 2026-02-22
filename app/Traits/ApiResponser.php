<?php

namespace App\Traits;

trait ApiResponser
{
    public function responser($data, $code = 200, $message = null)
    {
        return response()->json([
            "message" => $message,
            "data" => $data
        ], $code);
    }
}