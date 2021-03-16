![Stryber Logo](https://g8i2b2u8.rocketcdn.me/wp-content/uploads/2019/12/Stryber-white-logo-1.png)

# Stryber Metrics Collector for Laravel

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Usage](#usage)
- [Known Issues](#known-issues)

## Requirements

- PHP ^8.0
- Laravel ^8.0
- InfluxDB >= 2.0

## Installation

```bash
composer require stryber/laravel-metrics
```

## Configuration

You can publish the config files with the following command:

```bash
php artisan vendor:publish --tag="stryber-mectrics"
```

Check [examples](examples) folder for:
- [env.example](examples/.env.example) env file with variables you should use to configure the package - copy them to your .env
- [docker-compose.yml](examples/docker-compose.yml) docker-compose file with influxdb service example
- [main_dashboard.json](examples/main_dashboard.json) exported influxdb dashboard to import on new influxdb instance

## Usage

For now package supports 1 driver - influxdb and collects 3 metrics - Command, Request and Query.
Command and Query metrics will be collected automatically after you install the package.
To make Request metrics work you have to enable middleware called ```'collect-metrics'```.

Feel free to add your own metric classes to collect metrics in your project.
For this you should extend ```\Stryber\Metrics\Collectables\Metric``` class and, if you're using influxdb, make
transformer from ```\Stryber\Metrics\Drivers\Influx\Transformers\MetricTransformer``` or
```\Stryber\Metrics\Drivers\Influx\Transformers\Transformer``` based on your needs.
If so, don't forget to add your transformer's resolver to config.


## Known Issues
If you want to fill metric's storage with some data don't use laravel test setup.
Based of current state of ```stryber/laravel-uuid-helper``` and laravel test request realization you will have all
metrics with the same request-id.
