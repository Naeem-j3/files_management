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
            return [
                'status' => false,
                'message' => 'Password or email is incorrect',
            ];
        }

        $user = User::where('email', $request->email)->first();
        // Check if an FCM token is provided in the request
        if ($request->has('fcm_token')) {
            // Update the FCM token in the database
            $user->update([
                'fcm_token' => $request->fcm_token,
            ]);
        }
        $token = $user->createToken('appToken')->plainTextToken;
//dd($user,$token);
        return [
            'status'=>true,
            'data' => [ // Return only the necessary data
                'user' => $user,
                'token' => $token,
            ],
        ];
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'You are logged out']);
    }
}
