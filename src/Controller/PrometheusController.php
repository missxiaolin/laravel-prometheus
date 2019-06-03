<?php

namespace Lin\Prometheus\Controller;


use App\Http\Controllers\Controller;
use Illuminate\Http\Response;
use Prometheus\CollectorRegistry;
use Prometheus\RenderTextFormat;

class PrometheusController extends Controller
{
    /**
     * metric
     *
     * Expose metrics for prometheus
     *
     * @return Response
     */
    public function metrics()
    {
        $renderer = new RenderTextFormat();

        $registry = app()->make('prometheus');

        return response($renderer->render($registry->getMetricFamilySamples()))
            ->header('Content-Type', $renderer::MIME_TYPE);
    }
}
