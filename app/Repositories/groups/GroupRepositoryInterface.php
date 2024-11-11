<?php

namespace App\Repositories\groups;

interface GroupRepositoryInterface
{
    public function create(array $data);
    public function delete($groupId);
    public function find($groupId);
}
