<?php

namespace App\Services;

use App\Models\GroupUser;
use App\Models\User;
use App\Repositories\users\UserRepositoryInterface;
use GPBMetadata\Google\Api\Auth;

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
    public function getUsersExcludingGroup($groupId)
    {
        return User::whereDoesntHave('groups', function ($query) use ($groupId) {
            $query->where('group_id', $groupId);
        })->where('is_admin',0)->get();
    }

    public function showInvitaion($user)
    {

        $invitaion=$user->invitedGroups->load('owner');


        return $invitaion;
    }
}
