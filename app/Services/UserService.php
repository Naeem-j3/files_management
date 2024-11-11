<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\users\UserRepositoryInterface;

class UserService
{
    protected $userRepository;
    public function __construct(UserRepositoryInterface $userRepository){
        $this->userRepository=$userRepository;
    }
    // Get all users
    public function getAllUsers()
    {
        return  $this->userRepository->getAllUsers();
    }

    // Search users by name or email
    public function searchUsers($query)
    {
        return $this->userRepository->searchUserByNameOrEmail($query); // Search for users by name or email
    }
}
