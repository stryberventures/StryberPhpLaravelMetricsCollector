<?php

declare(strict_types=1);

namespace Stryber\Metrics\Drivers\Influx\Transformers;

use Closure;
use Stryber\Metrics\Collectables\CommandMetric;
use Stryber\Metrics\Collectables\Metric;
use Stryber\Metrics\Collectables\QueryMetric;
use Stryber\Metrics\Collectables\RequestMetric;

final class TransformersFactory
{
    /**
     * @var array<class-string, Closure(Metric):Transformer>
     */
    private array $metricTransformers;

    /**
     * @param array<class-string, Closure(Metric):Transformer> $additionalTransformers
     */
    public function __construct(array $additionalTransformers = [])
    {
        $this->metricTransformers = array_merge($this->getDefaultTransformers(), $additionalTransformers);
    }

    public function fromMetric(Metric $metric): Transformer
    {
        if (!isset($this->metricTransformers[$metric::class])) {
            throw UnsupportedMetric::fromMetric($metric);
        }

        return $this->metricTransformers[$metric::class]($metric);
    }

    private function getDefaultTransformers(): array
    {
        return [
            QueryMetric::class => fn(QueryMetric $metric) => new QueryMetricTransformer($metric),
            RequestMetric::class => fn(RequestMetric $metric) => new RequestMetricTransformer($metric),
            CommandMetric::class => fn(CommandMetric $metric) => new CommandMetricTransformer($metric),
        ];
    }
}
