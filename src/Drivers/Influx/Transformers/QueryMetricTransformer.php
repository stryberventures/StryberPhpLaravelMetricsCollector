<?php

declare(strict_types=1);

namespace Stryber\Metrics\Drivers\Influx\Transformers;

use InfluxDB2\Point;
use Stryber\Metrics\Collectables\QueryMetric;

final class QueryMetricTransformer extends MetricTransformer
{
    public function __construct(QueryMetric $metric)
    {
        parent::__construct($metric);
    }

    protected function populateFields(Point $point): Point
    {
        return $point->addField('created_at', $this->metric->getCreatedAt()->format('U'))
            ->addTag('query', $this->metric->getQuery())
            ->addField('duration', $this->metric->getDuration());
    }
}
