<?php

namespace Garbetjie\Laravel\HttpQueueWorker\Parsers;

use Illuminate\Contracts\Container\Container;

abstract class Parser
{
    protected Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }
}
