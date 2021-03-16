<?php

declare(strict_types=1);

namespace Stryber\Metrics\Collectables;

use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

final class CommandMetric extends Metric
{
    private float $duration;
    private string $command;

    public function __construct(
        UuidInterface $id,
        DateTimeInterface $created_at,
        float $duration,
        string $command
    ) {
        parent::__construct($id, $created_at);
        $this->duration = $duration;
        $this->command = $command;
    }

    public function getName(): string
    {
        return 'cli-command';
    }

    public function getDuration(): float
    {
        return $this->duration;
    }

    public function getCommand(): string
    {
        return $this->command;
    }
}
