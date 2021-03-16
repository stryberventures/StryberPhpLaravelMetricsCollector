<?php

declare(strict_types=1);

namespace Stryber\Metrics;

use Carbon\CarbonImmutable;
use Closure;
use DateTimeImmutable;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Contracts\Bus\Dispatcher as JobDispatcher;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Contracts\Foundation\Application as WebApplication;
use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\ServiceProvider;
use Stryber\Metrics\Collectables\CommandMetric;
use Stryber\Metrics\Collectables\QueryMetric;
use Stryber\Metrics\Drivers\Driver;
use Stryber\Metrics\Drivers\DriverManager;
use Stryber\Metrics\Drivers\Influx\Transformers\TransformersFactory;
use Stryber\Uuid\UuidGenerator;

final class MetricsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/stryber-metrics.php', 'stryber-metrics');
        $this->registerTimeControl($this->app['request'], microtime(true));
        $this->app->when(TransformersFactory::class)
            ->needs('$additionalTransformers')
            ->give($this->app['config']->get('stryber-metrics.drivers.influx.factory.additional_transformers'));
        $this->app->singleton(DriverManager::class);
        /** @var DriverManager $driverManager */
        $driverManager = $this->app->get(DriverManager::class);
        $this->app->singleton(Driver::class, fn() => $driverManager->driver());
        $this->app->singleton(Collector::class);
        $this->registerMiddleware($this->app['router']);
    }

    public function boot(
        Connection $db,
        UuidGenerator $uuidGenerator,
        Collector $collector,
        EventDispatcher $eventDispatcher,
        JobDispatcher $jobDispatcher,
        Repository $config,
        TimeControl $timeControl,
    ): void {
        $this->publishConfig();
        $this->registerQueryListener($db, $uuidGenerator, $collector);
        $this->registerCommandListener($eventDispatcher, $timeControl, $uuidGenerator, $collector);
        $queue = $config->get('stryber-metrics.job.submit.queue');
        $dispatchNow = (bool)$config->get('stryber-metrics.job.submit.use_queue');
        $this->registerTerminatingListener(
            $this->app,
            fn() => $this->submitMetrics($jobDispatcher, $collector, $dispatchNow, $queue)
        );
    }

    private function publishConfig(): void
    {
        $this->publishes(
            [
                __DIR__ . '/../config/stryber-metrics.php' => $this->app->configPath('stryber-metrics.php'),
            ],
            'stryber-metrics'
        );
    }

    public function registerTimeControl(Request $request, float $fallbackTime): void
    {
        $this->app->singleton(TimeControl::class, function () use ($request, $fallbackTime): TimeControl {
            $started = defined('LARAVEL_START') ?
                LARAVEL_START :
                (float)$request->server('REQUEST_TIME_FLOAT', $fallbackTime);
            return new TimeControl(DateTimeImmutable::createFromFormat('U.u', (string)$started));
        });
    }

    private function registerQueryListener(Connection $db, UuidGenerator $uuidGenerator, Collector $collector): void
    {
        $db->listen(function (QueryExecuted $event) use ($uuidGenerator, $collector): void {
            $metric = QueryMetric::fromEvent(
                $uuidGenerator->generate(),
                new CarbonImmutable(),
                $event
            );
            $collector->push($metric);
        });
    }

    private function registerCommandListener(
        EventDispatcher $dispatcher,
        TimeControl $timeControl,
        UuidGenerator $uuidGenerator,
        Collector $collector,
    ): void {
        $dispatcher->listen(
            CommandFinished::class,
            function (CommandFinished $event) use ($timeControl, $uuidGenerator, $collector) {
                $terminatedAt = new DateTimeImmutable();
                $metric = new CommandMetric(
                    $uuidGenerator->generate(),
                    $terminatedAt,
                    $timeControl->sinceStartTo($terminatedAt),
                    $event->command
                );
                $collector->push($metric);
            }
        );
    }

    private function registerTerminatingListener(WebApplication $app, Closure $jobRunner): void
    {
        //No implementation available here, so rely on dynamic nature of php
        if (method_exists($app, 'terminating')) {
            $app->terminating($jobRunner);
        }
    }

    private function submitMetrics(JobDispatcher $jobDispatcher, Collector $collector, bool $dispatchNow, string $queue): void
    {
        $job = new SubmitMetricsJob($collector, $queue);
        $dispatchNow ? $jobDispatcher->dispatchNow($job) : $jobDispatcher->dispatch($job);
    }

    private function registerMiddleware(Router $router): void
    {
        $router->aliasMiddleware('collect-metrics', CollectRequestMetrics::class);
    }
}
