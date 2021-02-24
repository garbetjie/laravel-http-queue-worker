<?php

namespace Garbetjie\Laravel\HttpQueueWorker\Handler;

use Closure;

trait ResolvesConnectionNames
{
    /**
     * @var Closure|null
     */
    protected $connectionNameResolver = null;

    /**
     * @param Closure|string|null $resolver
     */
    public function setConnectionNameResolver($resolver)
    {
        if (!$resolver instanceof Closure) {
            $this->connectionNameResolver = function() use ($resolver) {
                return $resolver;
            };
        } else {
            $this->connectionNameResolver = $resolver;
        }
    }

    /**
     * @return Closure|null
     */
    public function getConnectionNameResolver()
    {
        return $this->connectionNameResolver;
    }

    protected function resolveConnection()
    {

    }
}
