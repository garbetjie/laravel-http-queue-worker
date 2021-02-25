<?php


namespace Garbetjie\Laravel\HttpQueueWorker;

class ConnectionResolver
{
    public function __invoke(HttpJob $job)
    {
        return null;
    }
}
