<?php

namespace App\Providers;

use App\Interfaces\AuthServiceInterface;
use App\Interfaces\TodoServiceInterface;
use App\Services\AuthService;
use App\Services\TodoService;
use Illuminate\Support\ServiceProvider;

/**
 * Class AppServiceProvider
 *
 * Binds interfaces to their concrete implementations.
 * This is the only place in the app that knows which class
 * implements which interface — everything else depends on
 * the interface, making it easy to swap implementations later.
 *
 * @package App\Providers
 */
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // When something asks for AuthServiceInterface, give it AuthService
        $this->app->bind(AuthServiceInterface::class, AuthService::class);

        // When something asks for TodoServiceInterface, give it TodoService
        $this->app->bind(TodoServiceInterface::class, TodoService::class);
    }

    public function boot(): void
    {
        //
    }
}
