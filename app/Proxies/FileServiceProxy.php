<?php

namespace App\Proxies;

use App\Services\FileService;

class FileServiceProxy
{
    private $fileService;
    private $aspect;

    public function __construct(FileService $fileService, $aspect)
    {
        $this->fileService = $fileService;
        $this->aspect = $aspect;
    }

    public function __call($method, $arguments)
    {
        try {
            // Before method execution
            $this->aspect->before($method, $arguments);

            // Call the original service method
            $result = call_user_func_array([$this->fileService, $method], $arguments);

            // After method execution
            $this->aspect->after($method, $result);

            return $result;
        } catch (\Exception $e) {
            // On exception, apply aspect
            $this->aspect->onException($method, $e);
            throw $e;
        }
    }
}
