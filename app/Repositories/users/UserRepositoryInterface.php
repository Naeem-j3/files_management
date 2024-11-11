<?php

namespace App\Repositories\users;

interface UserRepositoryInterface
{
    public function getAllUsers();
public function create(array $data);
public function find($userId);
public function searchUserByNameOrEmail($query);
}
