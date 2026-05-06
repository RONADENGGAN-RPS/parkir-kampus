<?php

namespace App\Providers;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Failed;
use App\Listeners\LogSuccessfulLogin;
use App\Listeners\LogFailedLoginAttempt;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class AppEventServiceProvider extends ServiceProvider
{
    protected $listen = [];

    public function boot(): void
    {
        parent::boot();

        Event::listen(
            Login::class,
            [LogSuccessfulLogin::class, 'handle']
        );

        Event::listen(
            Failed::class,
            [LogFailedLoginAttempt::class, 'handle']
        );
    }
}
