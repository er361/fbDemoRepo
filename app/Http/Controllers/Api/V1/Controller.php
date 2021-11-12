<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Str;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * @param int $status
     * @param string $errors
     * @return JsonResponse
     */
    public function jsonError($status = 400, $errors = ''): JsonResponse
    {
        return response()->json([
            'errors' => $errors
        ], $status);
    }

    /**
     * @param int $status
     * @param string $message
     * @return JsonResponse
     */
    public function jsonSuccess($status = 200, $message = ''): JsonResponse
    {
        return response()->json([
            'data' => [
                'success' => $status == 200 || $status == 201,
                'message' => $message,
            ]
        ], $status);
    }
}
