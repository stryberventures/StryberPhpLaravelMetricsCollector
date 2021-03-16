<?php

use InfluxDB2\Model\WritePrecision;

return [
    'default' => env('METRICS_DRIVER', 'influx'),
    'job' => [
        'submit' => [
            'use_queue' => env('METRICS_JOB_SUBMIT_USE_QUEUE', true),
            'queue' => env('METRICS_JOB_SUBMIT_QUEUE_NAME', 'metrics'),
        ],
    ],
    'drivers' => [
        'influx' => [
            'client' => [
                'url' => env('METRICS_INFLUXDB_URL'),
                'token' => env('METRICS_INFLUXDB_TOKEN'),
                'bucket' => env('METRICS_INFLUXDB_BUCKET'),
                'org' => env('METRICS_INFLUXDB_ORG'),
                'precision' => WritePrecision::US,
            ],
            'factory' => [
                //class-string => Closure(Metric):Transformer
                'additional_transformers' => [

                ],
            ]
        ],
    ],
];
