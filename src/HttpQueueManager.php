<?php

namespace Garbetjie\Laravel\HttpQueueWorker;

use Closure;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use function get_class;
use function is_callable;

class HttpQueueManager
{
    /**
     * @var Closure[]
     */
    protected $handlers = [];

    /**
     * @var Container
     */
    protected $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Add a new handler for extracting jobs to the end of the list.
     *
     * @param string $name
     * @param Closure $fn
     */
    public function extend(string $name, Closure $fn)
    {
        $this->handlers[$name] = $fn;
    }

    /**
     * Add a new handler to the beginning of the list.
     *
     * @param string $name
     * @param Closure $fn
     */
    public function unshift(string $name, Closure $fn)
    {
        $this->handlers = [$name => $fn] + $this->handlers;
    }

    /**
     * Remove the specified handler if it is defined.
     *
     * @param string $name
     */
    public function remove(string $name)
    {
        unset($this->handlers[$name]);
    }

    /**
     * Runs the provided request through all the handlers, and returns the first job provided by any of the handlers.
     *
     * @param Request $request
     * @return Job|null
     */
    public function capture(Request $request): ?Job
    {
        foreach ($this->handlers as $name => $fn) {
            if ($job = $this->resolveJob($this->resolveParser($fn), $request)) {
                return $job;
            }
        }

        return null;
    }

    protected function resolveJob(?callable $parser, Request $request)
    {
        if (!$parser) {
            return null;
        }

        return $this->container->call($parser, [get_class($request) => $request]) ?: null;
    }

    protected function resolveParser(callable $fn)
    {
        if (is_callable($parser = $this->container->call($fn))) {
            return $parser;
        }

        return null;
    }
}
