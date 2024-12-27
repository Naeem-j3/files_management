<?php
namespace App\Proxies;

use App\Services\GroupService;

class GroupServiceProxy
{
    private $groupService;
    private $aspect;

    public function __construct(GroupService $groupService, $aspect)
    {
        $this->groupService = $groupService;
        $this->aspect = $aspect;
    }

    public function __call($method, $arguments)
    {
        try {
            // Before method execution
            $this->aspect->before($method, $arguments);

            // Call the original service method
            $result = call_user_func_array([$this->groupService, $method], $arguments);

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
