<?php

namespace Tests;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\NullQueue;
use ReflectionObject;

trait MakesJobPayload
{
    protected function makePayload($job): array
    {
        $queue = new NullQueue();

        $method = (new ReflectionObject($queue))->getMethod('createPayloadArray');
        $method->setAccessible(true);

        return $method->invoke($queue, $job, 'default');
    }

    protected function makePayloadString($job): string
    {
        $queue = new NullQueue();

        $method = (new ReflectionObject($queue))->getMethod('createPayload');
        $method->setAccessible(true);

        return $method->invoke($queue, $job, 'default');
    }
}
