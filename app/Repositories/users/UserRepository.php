<?php

namespace App\Repositories\users;

use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function getAllUsers()
    {
        return User::all();
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function find($userId)
    {
        return User::find($userId);
    }

    public function searchUserByNameOrEmail($query)
    {
        return User::where('name', 'like', '%' . $query . '%')
            ->orWhere('email', 'like', '%' . $query . '%')
            ->get();
    }
}
