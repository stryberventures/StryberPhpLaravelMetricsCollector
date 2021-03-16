<?php

declare(strict_types=1);

namespace Stryber\Metrics\Drivers;

use Illuminate\Support\Manager;
use InfluxDB2\Client;
use Psr\Log\LoggerInterface;
use Stryber\Metrics\Drivers\Influx\InfluxDriver;
use Stryber\Metrics\Drivers\Influx\Transformers\TransformersFactory;

final class DriverManager extends Manager
{
    public function getDefaultDriver(): string
    {
        return $this->config->get('stryber-metrics.default');
    }

    public function createInfluxDriver(): InfluxDriver
    {
        return new InfluxDriver(
            new Client($this->config->get('stryber-metrics.drivers.influx.client')),
            $this->container->get(TransformersFactory::class),
            $this->container->get(LoggerInterface::class)
        );
    }
}
