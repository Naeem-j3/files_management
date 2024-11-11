<?php

namespace App\Providers;


use App\Repositories\files\FileRepository;
use App\Repositories\files\FileRepositoryInterface;
use App\Repositories\groups\GroupRepository;
use App\Repositories\groups\GroupRepositoryInterface;
use App\Repositories\users\UserRepository;
use App\Repositories\users\UserRepositoryInterface;
use Illuminate\Support\ServiceProvider;
use Ytake\LaravelAspect\AspectManager;
use App\Aspects\LoggingAspect;

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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
}
