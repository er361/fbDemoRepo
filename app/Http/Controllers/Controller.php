<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function jsonError($status = 200, $errors = '')
    {
        return response()->json([
            'errors' => $errors
        ], $status);
    }

    public function jsonSuccess($status = 200, $message = '')
    {
        return response()->json([
            'data' => [
                'success' => $status == 200 || $status == 201,
                'message' => $message,
            ]
        ], $status);
    }
}
