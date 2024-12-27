<?php

namespace App\Http\Controllers;

use App\Proxies\UserServiceProxy;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserServiceProxy $userService)
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
    public function getUsersExcludingGroup($groupId)
    {
        $users = $this->userService->getUsersExcludingGroup($groupId);

        return response()->json([
            'status' => true,
            'data' => $users
        ], 200);
    }

    public function showInvitaion()
    {
        $user=Auth::user();
        $invitaions = $this->userService->showInvitaion($user);

        return response()->json([
            'status' => true,
            'data' => $invitaions
        ], 200);
    }

}
