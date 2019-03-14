<?php

namespace Welfordian\SeedMigrations;

use Illuminate\Support\ServiceProvider;
use Welfordian\SeedMigrations\Console\Seeds\UnseedCommand;

class SeedMigrationsProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands(['welfordian.seedmigrations.unseed']);

        $this->loadMigrationsFrom(__DIR__ . '/migrations');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->singleton('Welfordian\SeedMigrations\SeedLogger', function() {
            return new SeedLogger();
        });

        $this->app->singleton('welfordian.seedmigrations.unseed', function () {
            return new UnseedCommand();
        });
    }
}
