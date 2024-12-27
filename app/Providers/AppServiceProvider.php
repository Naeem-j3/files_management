<?php

namespace App\Providers;


use App\Aspects\Aspect;
use App\Aspects\AuthServiceAspect;
use App\Aspects\GroupServiceAspect;
use App\Proxies\AuthServiceProxy;
use App\Proxies\FileLogServiceProxy;
use App\Proxies\FileServiceProxy;
use App\Proxies\GroupServiceProxy;
use App\Proxies\UserServiceProxy;
use App\Repositories\files\FileRepository;
use App\Repositories\files\FileRepositoryInterface;
use App\Repositories\groups\GroupRepository;
use App\Repositories\groups\GroupRepositoryInterface;
use App\Repositories\users\UserRepository;
use App\Repositories\users\UserRepositoryInterface;
use App\Services\AuthService;
use App\Services\FileLogService;
use App\Services\FileService;
use App\Services\GroupService;
use App\Services\UserService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(GroupRepositoryInterface::class, GroupRepository::class);
        $this->app->bind(FileRepositoryInterface::class, FileRepository::class);
        $this->app->bind(UserRepositoryInterface::class,UserRepository::class);

        // Bind the original AuthService
        $this->app->singleton(AuthService::class, function ($app) {
            return new AuthService($app->make(\App\Repositories\users\UserRepositoryInterface::class));
        });

        // Bind the proxy to replace the original service
        $this->app->singleton(AuthServiceProxy::class, function ($app) {
            return new AuthServiceProxy(
                $app->make(AuthService::class),
                $app->make(AuthServiceAspect::class)
            );
        });

        // Bind the original GroupService
        $this->app->singleton(GroupService::class, function ($app) {
            return new GroupService(
                $app->make(\App\Repositories\groups\GroupRepositoryInterface::class),
                $app->make(\App\Repositories\files\FileRepositoryInterface::class),
                $app->make(\App\Services\NotificationService::class)
            );
        });

        // Bind the proxy to replace the original service
        $this->app->singleton(GroupServiceProxy::class, function ($app) {
            return new GroupServiceProxy(
                $app->make(GroupService::class),
                $app->make(GroupServiceAspect::class)
            );
        });

        $this->app->singleton(UserService::class, function ($app) {
            return new UserService(
                $app->make(\App\Repositories\users\UserRepositoryInterface::class),
                $app->make(\App\Services\NotificationService::class)
            );
        });
        $this->app->singleton(UserServiceProxy::class, function ($app) {
            return new UserServiceProxy(
                $app->make(UserService::class),
                $app->make(Aspect::class)
            );
        });
        $this->app->singleton(FileServiceProxy::class, function ($app) {
            return new FileServiceProxy(
                $app->make(FileService::class),
                $app->make(Aspect::class)
            );
        });
        $this->app->singleton(FileLogServiceProxy::class, function ($app) {
            return new FileLogServiceProxy(
                $app->make(FileLogService::class),
                $app->make(Aspect::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
}
