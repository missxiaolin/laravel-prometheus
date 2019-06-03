<?php

Route::get('/prometheus/metrics', \Lin\Prometheus\Controller\PrometheusController::class . '@metrics')->name('prometheus.metrics');
