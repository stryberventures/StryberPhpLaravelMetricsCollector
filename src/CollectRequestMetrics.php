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
    private Collector $collector;
    private UuidGenerator $uuidGenerator;
    private float $fallbackTime;

    public function __construct(Collector $collector, UuidGenerator $uuidGenerator)
    {
        $this->collector = $collector;
        $this->uuidGenerator = $uuidGenerator;
        $this->fallbackTime = microtime(true);
    }

    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        $terminatedAt = new DateTimeImmutable();
        $startedAt = defined('LARAVEL_START') ?
            LARAVEL_START :
            (float)$request->server('REQUEST_TIME_FLOAT', $this->fallbackTime);
        $metric = new RequestMetric(
            $this->uuidGenerator->generate(),
            $terminatedAt,
            (float)$terminatedAt->format('U') - $startedAt,
            $request->path(),
            $request->ip()
        );
        $this->collector->push($metric);
    }
}
