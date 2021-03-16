<?php

namespace Stryber\Metrics\Drivers\Influx\Transformers;

use InfluxDB2\Point;

interface Transformer
{
    public function transform(): Point;
}
