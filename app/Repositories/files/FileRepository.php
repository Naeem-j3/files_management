<?php

namespace App\Repositories\files;

use App\Models\File;
use App\Models\FileBackup;
use App\Models\FileCheckout;

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

    public function createCheckin($fileId,$userId)
    {
        return  FileCheckout::create([
            'file_id' => $fileId,
            'user_id' => $userId,
            'checked_out_at' => null,
            'checked_in_at' => now()
        ]);
    }
    public function createBackUp($fileId,$backupPath){
        FileBackup::create([
            'file_id' => $fileId,
            'backup_path' => $backupPath,
            'created_at' => now()
        ]);
    }
}
