<?php

declare(strict_types=1);

namespace Stryber\Metrics\Drivers\Influx\Transformers;

use InfluxDB2\Point;
use Stryber\Metrics\Collectables\CommandMetric;

final class CommandMetricTransformer extends MetricTransformer
{
    public function __construct(CommandMetric $metric)
    {
        parent::__construct($metric);
    }

    protected function populateFields(Point $point): Point
    {
        return $point->addTag('command', $this->metric->getCommand())
            ->addField('duration', $this->metric->getDuration());
    }
}
