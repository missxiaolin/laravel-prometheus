<?php

namespace Lin\Prometheus;

use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Lin\Prometheus\Adapter\RedisAdapter;
use Prometheus\CollectorRegistry;
use Prometheus\Storage\Redis;

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

            if (!isset($config['client']) || $config['client'] == 'predis') {
                return new CollectorRegistry(new RedisAdapter($config));
            }


            $options = Arr::get(app(), 'config.database.redis.'.$config['conn']);

            Redis::setPrefix($config['prefix']);

            return new CollectorRegistry(new Redis($options));
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