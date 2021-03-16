<?php

declare(strict_types=1);

namespace Stryber\Metrics\Drivers\Influx;

use InfluxDB2\Client;
use InfluxDB2\WriteApi;
use InfluxDB2\WriteType;
use Psr\Log\LoggerInterface;
use Stryber\Metrics\Drivers\Driver;
use Stryber\Metrics\Drivers\Influx\Transformers\TransformersFactory;
use Stryber\Metrics\Drivers\Influx\Transformers\UnsupportedMetric;
use Stryber\Metrics\Metrics;

final class InfluxDriver implements Driver
{
    private Client $client;
    private TransformersFactory $factory;
    private LoggerInterface $logger;

    public function __construct(Client $client, TransformersFactory $factory, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->factory = $factory;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function submit(Metrics $metrics): void
    {
        $points = $this->toPoints($metrics);
        $writer = $this->createWriter(count($points));
        foreach ($points as $point) {
            $writer->write($point);
        }
        $writer->close();
    }

    private function toPoints(Metrics $metrics): array
    {
        $points = [];
        foreach ($metrics as $metric) {
            try {
                $transformer = $this->factory->fromMetric($metric);
            } catch (UnsupportedMetric $e) {
                $this->logger->error($e->getMessage());
                continue;
            }
            $points[] = $transformer->transform();
        }
        return $points;
    }

    private function createWriter(int $batchSize): WriteApi
    {
        return $this->client->createWriteApi(
            [
                'writeType' => WriteType::BATCHING,
                'batchSize' => $batchSize,
            ]
        );
    }
}
