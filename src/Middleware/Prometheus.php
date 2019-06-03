<?php

namespace Lin\Prometheus\Middleware;

use Closure;
use Prometheus\CollectorRegistry;
use Prometheus\Histogram;

class Prometheus
{

    /**
     * @var Histogram
     */
    protected $histogram;

    /**
     * @var CollectorRegistry
     */
    protected $registry;

    public function __construct()
    {
        $this->registry = app()->make('prometheus');
        $this->initRouteMetrics();
    }

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $start = LARAVEL_START;
        /** @var \Illuminate\Http\Response $response */
        $response = $next($request);
        $route_name = $this->getRouteName();

        if($route_name == 'unknow'){
            return $next($request);
        }

        $method = $request->getMethod();
        $status = $response->getStatusCode();
        $duration = microtime(true) - $start;
        $duration_milliseconds = $duration * 1000.0;
        $this->countRequest($route_name, $method, $status, $duration_milliseconds);
        return $response;
    }

    /**
     * Get route name
     *
     * @return string
     */
    protected function getRouteName()
    {
        return \Route::currentRouteName() ?: 'unknow';
    }

    protected function initRouteMetrics()
    {
        $namespace = config('prometheus.namespace');
        $buckets = config('prometheus.histogram_buckets');

        $labelNames = $this->getRequestCounterLabelNames();


        $name = config('prometheus.name');
        $help = config('prometheus.help');
        $this->histogram = $this->registry->getOrRegisterHistogram(
            $namespace, $name, $help, $labelNames, $buckets
        );
    }

    protected function getRouteNames()
    {
        $routeNames = [];
        foreach (\Route::getRoutes() as $route) {
            $routeNames[] = $route->getName() ?: "unknow";
        }
        return $routeNames;
    }

    protected function countRequest($route, $method, $statusCode, $duration_milliseconds)
    {
        $labelValues = [(string)$route, (string)$method, (string)$statusCode];
        $this->histogram->observe($duration_milliseconds, $labelValues);
    }

    protected function getRequestCounterLabelNames()
    {
        return [
            'route', 'method', 'status_code',
        ];
    }
}
