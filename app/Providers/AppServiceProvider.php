<?php

namespace App\Providers;

use App\Repositories\MessagesRepositoryInterface;
use App\Repositories\Xml\MessagesRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(MessagesRepositoryInterface::class, MessagesRepository::class);
    }
}
