<?php

namespace App\Aspects;

use Illuminate\Support\Facades\Log;

class Aspect
{
    public function before($method, $params)
    {
        Log::info("Before method: {$method}", ['params' => $params]);
    }

    public function after($method, $result)
    {
        Log::info("After method: {$method}", ['result' => $result]);
    }

    public function onException($method, $exception)
    {
        Log::error("Exception in method: {$method}", ['exception' => $exception->getMessage()]);
    }
}
