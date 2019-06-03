<?php

namespace Lin\Prometheus;

use Illuminate\Support\ServiceProvider;
use Lin\Prometheus\Adapter\RedisAdapter;
use Prometheus\CollectorRegistry;

class LaravelProvider extends ServiceProvider
{

    protected $defer = true;

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config' => config_path()], 'prometheus');
        }

        $this->app->singleton('prometheus', function ($app) {
            $config = $app['config']['prometheus'];
            return new CollectorRegistry(new RedisAdapter($config));
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerMetricsRoute();
    }

    public function registerMetricsRoute()
    {
        $this->loadRoutesFrom(__DIR__ . '/laravel_routes.php');
    }

    public function provides()
    {
        return ['prometheus'];
    }
}
