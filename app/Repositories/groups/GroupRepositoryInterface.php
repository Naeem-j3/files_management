<?php

namespace App\Repositories\groups;

interface GroupRepositoryInterface
{
    public function create(array $data);
    public function update($groupId,$groupName);
    public function delete($groupId);
    public function find($groupId);
}
