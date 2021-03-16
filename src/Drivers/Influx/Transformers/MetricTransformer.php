<?php

declare(strict_types=1);

namespace Stryber\Metrics\Drivers\Influx\Transformers;

use InfluxDB2\Point;
use Stryber\Metrics\Collectables\Metric;

abstract class MetricTransformer implements Transformer
{
    protected Metric $metric;

    public function __construct(Metric $metric)
    {
        $this->metric = $metric;
    }

    abstract protected function populateFields(Point $point): Point;

    final public function transform(): Point
    {
        $point = $this->makePoint();
        return $this->populateFields($point);
    }

    private function makePoint(): Point
    {
        return Point::measurement($this->metric->getName())
            ->addTag('id', $this->metric->getId()->toString())
            ->time($this->metric->getCreatedAt()->format('Uu'));
    }
}
