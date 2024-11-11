<?php

namespace App\Http\Controllers\auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    // Inject the AuthService
    public function __construct(AuthService $authService)
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
        $data =$this->authService->login($request);

        return response()->json([
            'status' => true,
            'data' => $data,
            'message' => 'User logged in successfully',
        ], 200);
    }

    // Logout a user
    public function logout(Request $request)
    {
        return $this->authService->logout($request);
    }
}
