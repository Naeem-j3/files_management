<?php

namespace App\Repositories\files;

interface FileRepositoryInterface
{
    public function create(array $data);
    public function delete($fileId);
    public function find($fileId);
}
