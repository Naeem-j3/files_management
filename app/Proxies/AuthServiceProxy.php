<?php

namespace App\Proxies;

use App\Services\AuthService;

class AuthServiceProxy
{
    private $authService;
    private $aspect;

    public function __construct(AuthService $authService, $aspect)
    {
        $this->authService = $authService;
        $this->aspect = $aspect;
    }

    public function __call($method, $arguments)
    {
        try {
            // Before logic
            $this->aspect->before($method, $arguments);

            // Call the original service method
            $result = call_user_func_array([$this->authService, $method], $arguments);

            // After logic
            $this->aspect->after($method, $result);

            return $result;
        } catch (\Exception $e) {
            // On exception logic
            $this->aspect->onException($method, $e);
            throw $e;
        }
    }
}
