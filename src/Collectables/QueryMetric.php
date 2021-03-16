<?php

declare(strict_types=1);

namespace Stryber\Metrics\Collectables;

use DateTimeInterface;
use Illuminate\Database\Events\QueryExecuted;
use Ramsey\Uuid\UuidInterface;

final class QueryMetric extends Metric
{
    private float $duration;
    private string $query;

    public function __construct(
        UuidInterface $id,
        DateTimeInterface $created_at,
        float $duration,
        string $query
    ) {
        parent::__construct($id, $created_at);
        $this->duration = $duration;
        $this->query = $query;
    }

    public static function fromEvent(
        UuidInterface $id,
        DateTimeInterface $createdAt,
        QueryExecuted $event,
    ): self {
        return new self(
            $id,
            $createdAt,
            $event->time ?? 0.0,
            $event->sql,
        );
    }

    public function getName(): string
    {
        return 'sql-query';
    }

    public function getDuration(): float
    {
        return $this->duration;
    }

    public function getQuery(): string
    {
        return $this->query;
    }
}
