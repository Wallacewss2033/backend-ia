<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            \App\Repositories\Contracts\UserRepositoryInterface::class,
            \App\Repositories\Eloquent\UserRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\ChatRepositoryInterface::class,
            \App\Repositories\Eloquent\ChatRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\DocumentRepositoryInterface::class,
            \App\Repositories\Eloquent\DocumentRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\AppointmentRepositoryInterface::class,
            \App\Repositories\AppointmentRepository::class
        );

        $this->app->bind(
            \App\Repositories\Contracts\CategoryRepositoryInterface::class,
            \App\Repositories\Eloquent\CategoryRepository::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \App\Models\Appointment::observe(\App\Observers\AppointmentObserver::class);
        \App\Models\User::observe(\App\Observers\UserObserver::class);
    }
}
