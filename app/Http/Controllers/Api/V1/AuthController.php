<?php

namespace App\Http\Controllers\Api\V1;

use App\Events\Login;
use App\Events\Logout;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|email',
            'password' => 'required|string|min:5',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$token = Auth::attempt($validator->validated())) {
            // @codeCoverageIgnoreStart
            return response()->json(['error' => 'Unauthorized'], 401);
            // @codeCoverageIgnoreEnd
        }

        Login::dispatch(Auth::id(), $token);

        return $this->createNewToken($token);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'display_name' => 'nullable|string|between:2,255',
            'username' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:5',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // @codeCoverageIgnoreStart
        $message = $this->checkLimiter($request);
        if ($message) {
            return response()->json(['message' => $message], 429);
        }
        // @codeCoverageIgnoreEnd

        $user = User::create(
            array_merge(
                $validator->validated(),
                ['password' => bcrypt($request->password)]
            )
        );

        return response()->json([
            'data' => $user
        ], 201);
    }


    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        Auth::logout();
        Logout::dispatch(Auth::id());
        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->createNewToken(Auth::refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile()
    {
        return response()->json(Auth::user());
    }

    /**
     * Get the token array structure.
     *
     * @param string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token)
    {
        return response()->json([
            'data' => [
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => Auth::factory()->getTTL() * 60,
                'user' => Auth::user()
            ]
        ]);
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    public function checkLimiter(Request $request)
    {
        $key = 'register_by_ip:' . $request->ip();

        RateLimiter::attempt(
            $key,
            3,
            function () {
                // Send message...
            },
            60 * 60
        );

        // @codeCoverageIgnoreStart
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            return 'You may try again in ' . $seconds . ' seconds.';
        }
        // @codeCoverageIgnoreEnd
        return null;
    }

}
