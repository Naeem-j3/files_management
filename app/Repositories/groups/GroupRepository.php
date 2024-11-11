<?php

namespace App\Repositories\groups;

use App\Models\Group;

class GroupRepository implements GroupRepositoryInterface
{

    public function create(array $data)
    {
        return Group::create($data);
    }

    public function delete($groupId)
    {
        return Group::delete($groupId);
    }

    public function find($groupId)
    {
        return Group::find($groupId);
    }


}
