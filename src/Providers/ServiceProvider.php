<?php

namespace Metko\Galera\Providers;

use Metko\Galera\Galera;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Service provider.
 */
class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        $src = dirname(__DIR__).'/';
        //dd($src.'config/galera.php');
        $this->publishes([
            $src.'config/galera.php' => config_path('galera.php'),
        ], 'config');
        $this->loadMigrationsFrom($src.'database/migrations');
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        if (app()->environment() == 'testing') {
            $config = require dirname(__DIR__).'/config/galera.php';
            config(['galera' => $config]);
            config(['galera.user_class' => "Tests\Models\User"]);
        }
        $this->app->singleton('galera', function () {
            return new Galera();
        });
    }
}
