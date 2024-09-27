<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        $this->authService->registerUser($request->only('username', 'email', 'password'));

        return response()->json(['message' => 'Registration successful'], 201);
    }

    public function login(LoginRequest $request)
    {
        try {
            $token = $this->authService->authenticateUser($request->only('email', 'password'));

            return response()->json(['access_token' => $token, 'token_type' => 'Bearer']);

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 401);
        }
    }

    public function logout()
    {
        $this->authService->logoutUser(Auth::user());
        return response()->json(['message' => 'Logged out successfully']);
    }
}
