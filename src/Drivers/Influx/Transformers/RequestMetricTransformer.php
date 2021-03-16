<?php

declare(strict_types=1);

namespace Stryber\Metrics\Drivers\Influx\Transformers;

use InfluxDB2\Point;
use Stryber\Metrics\Collectables\RequestMetric;

final class RequestMetricTransformer extends MetricTransformer
{
    public function __construct(RequestMetric $metric)
    {
        parent::__construct($metric);
    }

    protected function populateFields(Point $point): Point
    {
        return $point->addField('created_at', $this->metric->getCreatedAt()->format('U'))
            ->addField('duration', $this->metric->getDuration())
            ->addTag('endpoint', $this->metric->getEndpoint())
            ->addField('ip', $this->metric->getIp());
    }
}
