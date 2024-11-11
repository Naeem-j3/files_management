<?php

namespace App\Repositories\files;

use App\Models\File;

class FileRepository implements FileRepositoryInterface
{

    public function create(array $data)
    {
        return File::create($data);
    }

    public function delete($fileId)
    {
        return $this->find($fileId)->delete();
//        return File::where('id',$fileId)->delete();

    }

    public function find($fileId)
    {
        return File::find($fileId);
    }
}
