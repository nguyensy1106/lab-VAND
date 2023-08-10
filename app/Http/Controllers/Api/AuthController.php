<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Models\User;
use Auth;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    //
    public function register(RegisterRequest $request) : JsonResponse
    {
        $data = $request->only('name', 'email', 'password');

        $user = User::create(array_merge(
            $data,
            ['password' => bcrypt($data['password'])]
        ));

        return response()->json([
            'status' => true,
            'message' => 'User registered successfully',
            'data' => $user
        ], 201);
    }

    public function login(LoginRequest $request) : JsonResponse
    {
        $data = $request->only('email', 'password');

        if (!$token = auth()->attempt($data)) {
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        return $this->createNewToken($token);
    }

    public function createNewToken($token) : JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => 'Login successful',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth()->factory()->getTTL()*60,
            'user' => auth()->user()
        ]);
    }

    public function profile() : JsonResponse
    {
        return response()->json([
            'status' => true,
            'message' => '',
            'data' => auth()->user()
        ]);
    }
}
