<?php

namespace App\Proxies;

use App\Services\UserService;

class UserServiceProxy
{
    private $userService;
    private $aspect;

    public function __construct(UserService $userService, $aspect)
    {
        $this->userService = $userService;
        $this->aspect = $aspect;
    }
    public function __call($method, $arguments)
    {
        try {
            // Before method execution
            $this->aspect->before($method, $arguments);

            // Call the original service method
            $result = call_user_func_array([$this->userService, $method], $arguments);

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
