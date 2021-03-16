<?php

declare(strict_types=1);

namespace Stryber\Metrics\Collectables;

use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

abstract class Metric
{
    private UuidInterface $id;
    private DateTimeInterface $created_at;

    public function __construct(UuidInterface $id, DateTimeInterface $created_at)
    {
        $this->id = $id;
        $this->created_at = $created_at;
    }

    abstract public function getName(): string;

    final public function getId(): UuidInterface
    {
        return $this->id;
    }

    final public function getCreatedAt(): DateTimeInterface
    {
        return $this->created_at;
    }
}
