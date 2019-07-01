<?php

namespace Metko\Galera\Providers;

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
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        $this->loadRoutesFrom(dirname(dirname(__DIR__)).'/routes/routes.php');
        $this->loadViewsFrom(__DIR__.'/../views', 'packagename');
        $this->publishes([
            __DIR__.'/../views', resource_path('views/vendor/packagename'),
        ]);
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->app->singleton("Galera\User", function ($app) {
            if (app()->environment() == 'testing') {
                return new \Metko\Galera\Tests\User();
            }

            //return new $config['user']();
        });
    }
}
