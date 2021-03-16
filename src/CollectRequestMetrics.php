<?php

declare(strict_types=1);

namespace Stryber\Metrics;

use Closure;
use DateTimeImmutable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stryber\Metrics\Collectables\RequestMetric;
use Stryber\Uuid\UuidGenerator;

final class CollectRequestMetrics
{
    private TimeControl $timeControl;
    private Collector $collector;
    private UuidGenerator $uuidGenerator;

    public function __construct(TimeControl $timeControl, Collector $collector, UuidGenerator $uuidGenerator)
    {
        $this->timeControl = $timeControl;
        $this->collector = $collector;
        $this->uuidGenerator = $uuidGenerator;
    }

    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        $terminatedAt = new DateTimeImmutable();
        $metric = new RequestMetric(
            $this->uuidGenerator->generate(),
            $terminatedAt,
            $this->timeControl->sinceStartTo($terminatedAt),
            $request->path(),
            $request->ip()
        );
        $this->collector->push($metric);
    }
}
