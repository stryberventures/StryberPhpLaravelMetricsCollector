<?php

declare(strict_types=1);

namespace Stryber\Metrics\Collectables;

use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

final class CommandMetric extends Metric
{
    private float $executionTime;
    private string $command;

    public function __construct(
        UuidInterface $id,
        DateTimeInterface $created_at,
        float $duration,
        string $command
    ) {
        parent::__construct($id, $created_at);
        $this->executionTime = $duration;
        $this->command = $command;
    }

    public function getName(): string
    {
        return 'command';
    }

    public function getExecutionTime(): float
    {
        return $this->executionTime;
    }

    public function getCommand(): string
    {
        return $this->command;
    }
}
