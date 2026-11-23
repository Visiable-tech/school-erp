<?php

namespace App\Providers;

use App\Listeners\RecordErpAuthentication;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class ErpActivityServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(Login::class, [RecordErpAuthentication::class, 'handle']);
        Event::listen(Logout::class, [RecordErpAuthentication::class, 'handle']);
        Event::listen(Failed::class, [RecordErpAuthentication::class, 'handle']);
    }
}
