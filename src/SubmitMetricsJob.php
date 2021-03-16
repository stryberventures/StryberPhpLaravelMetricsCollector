<?php

declare(strict_types=1);

namespace Stryber\Metrics;

use Illuminate\Contracts\Queue\ShouldQueue;
use Stryber\Metrics\Drivers\Driver;

final class SubmitMetricsJob implements ShouldQueue
{
    private Collector $collector;
    public string $queue;

    public function __construct(Collector $collector, string $queue)
    {
        $this->collector = $collector;
        $this->queue = $queue;
    }

    public function handle(Driver $driver): void
    {
        $driver->submit($this->collector->getMetrics());
    }
}
