<?php

declare(strict_types=1);

namespace Stryber\Metrics;

use Stryber\Metrics\Collectables\Metric;

final class Collector
{
    private Metrics $metrics;

    public function __construct()
    {
        $this->metrics = new Metrics();
    }

    public function push(Metric $metric): void
    {
        $this->metrics->push($metric);
    }

    public function getMetrics(): Metrics
    {
        return $this->metrics;
    }
}
