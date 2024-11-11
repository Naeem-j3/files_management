<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\users\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected $userRepository;
    public function __construct(UserRepositoryInterface $userRepository){
        $this->userRepository=$userRepository;
    }
    // Register a new user
    public function register(Request $request)
    {

        $user =$this->userRepository->create([
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'fcm_token' => $request['fcm_token'] ?? null,
        ]);

        $token = $user->createToken('appToken')->plainTextToken;
//        Mail::to($user->email)->send(new VerifyEmail($token));
        return [
            'user' => $user,
            'token' => $token
        ];

    }

    public function login(Request $request)
    {

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status'=>false,
                'data' => [],
                'message' => 'Password or email is incorrect',
            ],400);
        }

        $user = User::where('email', $request->email)->first();
        $token = $user->createToken('appToken')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token
        ];
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'You are logged out']);
    }
}
