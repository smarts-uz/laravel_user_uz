<?php

namespace App\Services;

trait Response
{
    public function success($data = [], $msg = "Muvaffaqiyatli") {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $msg
        ]);
    }

    protected function fail($errors = [], $msg = "Muvaffaqiyatsiz") {
        return response()->json([
            'success' => false,
            'data' => $errors,
            'message' => $msg
        ]);
    }
}
