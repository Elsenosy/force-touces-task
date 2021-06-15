<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GeneralController;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Resources\UserResource;

class AuthController extends GeneralController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
        $this->guard = 'api';
    }

    /**
     * Get a JWT token via given credentials.
     *
     * @param  \Illuminate\Http\Requests/Api/LoginRequest  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if ($token = auth('api')->attempt($credentials)) {
            return $this->respondWithToken($token);
        }

        return $this->returnError(trans('auth.failed'));
    }

    /**
     * Get the authenticated User
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        $user = $this->userApi();
        return $this->returnSuccess(null, new UserResource($user));      
    }

    /**
     * Log the user out (Invalidate the token)
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $this->guard()->logout();

        return $this->returnSuccess("Successfully logged out");
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        // Get logged user
        $user = $this->userApi();

        // Get user data into the payload
        $payload = $user->select('id', 'name', 'email')->first()->toArray();
        
        // Append the token and token type to the payload 
        $payload['token'] = $token;
        $payload['token_type'] = 'bearer';

        return $this->returnSuccess("Logged in", $payload);
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\Guard
     */
    public function guard()
    {
        return \Auth::guard('api');
    }
}
