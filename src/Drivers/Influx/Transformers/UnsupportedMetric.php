<?php

declare(strict_types=1);

namespace Stryber\Metrics\Drivers\Influx\Transformers;

use InvalidArgumentException;
use Stryber\Metrics\Collectables\Metric;

final class UnsupportedMetric extends InvalidArgumentException
{
    public static function fromMetric(Metric $metric): self
    {
        return new self("Can't find transformer for metric: {$metric->getName()}, {$metric::class}");
    }
}
