<?php

declare(strict_types=1);

namespace Stryber\Metrics\Collectables;

use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

final class RequestMetric extends Metric
{
    private float $duration;
    private string $endpoint;
    private string $ip;

    public function __construct(
        UuidInterface $id,
        DateTimeInterface $created_at,
        float $duration,
        string $endpoint,
        string $ip
    ) {
        parent::__construct($id, $created_at);
        $this->duration = $duration;
        $this->endpoint = $endpoint;
        $this->ip = $ip;
    }

    public function getName(): string
    {
        return 'http-request';
    }

    public function getDuration(): float
    {
        return $this->duration;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function getIp(): string
    {
        return $this->ip;
    }
}
