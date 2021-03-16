<?php

namespace Stryber\Metrics\Drivers;

use Stryber\Metrics\Metrics;

interface Driver
{
    public function submit(Metrics $metrics): void;
}
