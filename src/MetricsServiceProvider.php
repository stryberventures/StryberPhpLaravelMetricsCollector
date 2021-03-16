<?php

declare(strict_types=1);

namespace Stryber\Metrics;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\Console\Application as ConsoleApplication;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application as WebApplication;
use Illuminate\Database\Connection;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\ServiceProvider;
use Stryber\Metrics\Collectables\QueryMetric;
use Stryber\Metrics\Drivers\Driver;
use Stryber\Metrics\Drivers\DriverManager;
use Stryber\Uuid\UuidGenerator;

final class MetricsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/stryber-metrics.php', 'stryber-metrics');
        $this->app->singleton(DriverManager::class);
        /** @var DriverManager $driverManager */
        $driverManager = $this->app->get(DriverManager::class);
        $this->app->singleton(Driver::class, $driverManager->driver());
        $this->app->singleton(Collector::class);
    }

    public function boot(
        Connection $db,
        UuidGenerator $uuidGenerator,
        Collector $collector,
        Dispatcher $dispatcher
    ): void {
        $this->publishConfig();
        $this->registerQueryListener($db, $uuidGenerator, $collector);
        $this->registerTerminatingListener($dispatcher);
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

    private function registerTerminatingListener(Dispatcher $dispatcher): void
    {
        match ($this->app::class) {
            WebApplication::class => $this->registerWebTerminatingListener($this->app),
            ConsoleApplication::class => $this->registerConsoleTerminatingListener($dispatcher),
            default => null,
        };
    }

    private function registerWebTerminatingListener(WebApplication $app): void
    {
        $app->terminating(function () use ($app) {
            $app->get();
        });
    }

    private function registerConsoleTerminatingListener(Dispatcher $dispatcher): void
    {

    }
}
