<?php

declare(strict_types=1);

namespace Stryber\Metrics;

use Illuminate\Contracts\Queue\ShouldQueue;
use Stryber\Metrics\Drivers\Driver;

final class SubmitMetricsJob implements ShouldQueue
{
    public function __construct(
        private Collector $collector
    ) {
    }

    public function handle(Driver $driver): void
    {
        $driver->submit($this->collector->getMetrics());
    }
}
