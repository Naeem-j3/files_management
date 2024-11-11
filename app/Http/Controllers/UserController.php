<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    // Get all users
    public function getAllUsers()
    {
        $users = $this->userService->getAllUsers();

        return response()->json([
            'status' => true,
            'data' => $users
        ], 200);
    }

    // Search users by name or email
    public function searchUsers(Request $request)
    {
        $query = $request->input('query'); // Get search query from request

        if (!$query) {
            return response()->json([
                'status' => false,
                'message' => 'Search query is required.'
            ], 400);
        }

        $users = $this->userService->searchUsers($query);

        return response()->json([
            'status' => true,
            'data' => $users
        ], 200);
    }
}
