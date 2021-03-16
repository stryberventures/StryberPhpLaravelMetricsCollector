<?php

return [
    'default' => env('METRICS_DRIVER', 'influxdb'),
    'drivers' => [
        'influxdb' => [
            'client' => [
                'url' => env('METRICS_INFLUXDB_URL'),
                'token' => env('METRICS_INFLUXDB_TOKEN'),
                'bucket' => env('METRICS_INFLUXDB_BUCKET'),
                'org' => env('METRICS_INFLUXDB_ORG'),
            ],
            'factory' => [
                'additional_transformers' => [

                ],
            ]
        ],
    ],
];
