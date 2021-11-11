<?php

namespace App\Exceptions;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Str;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var string[]
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var string[]
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        $this->renderable(function (QueryException $e, $request) {
            if (Str::contains($e->getMessage(), 'Duplicate entry')) {
                return response()->json([
                    'message' => 'Duplicate entry',
                    'errors' => $e->getMessage()
                ], 400);
            }
        });
    }
}
