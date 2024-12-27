<?php
namespace App\Aspects;

use Illuminate\Support\Facades\Log;

class GroupServiceAspect
{
    // Before method execution
    public function before($method, $params)
    {
        Log::info("Before method: {$method}", ['params' => $params]);
    }

    // After method execution
    public function after($method, $result)
    {
        Log::info("After method: {$method}", ['result' => $result]);
    }

    // Exception handling
    public function onException($method, $exception)
    {
        Log::error("Exception in method: {$method}", ['exception' => $exception->getMessage()]);
    }
}
