<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Proxies\AuthServiceProxy;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthServiceProxy $authService)
    {
        $this->authService = $authService;
    }

    // Register a new user
    public function register(RegistrationRequest $request)
    {
        $data = $this->authService->register($request);

        return response()->json([
            'status' => true,
            'data' => $data,
            'message' => 'User registered successfully',
        ], 200);
    }

    // Login a user
    public function login(LoginRequest $request)
    {
        $response =$this->authService->login($request);

        if (!$response['status']) {
            return response()->json([
                'status' => false,
                'message' => $response['message'],
            ], 400);
        }

        return response()->json([
            'status' => true,
            'data' => $response['data'],
            'message' => 'User logged in successfully',
        ], 200);
    }

    // Logout a user
    public function logout(Request $request)
    {
        return $this->authService->logout($request);
    }
}
