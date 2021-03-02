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
    protected $parsers = [];

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
     * Add a new parser for resolving jobs to the end of the list.
     *
     * @param string $name
     * @param Closure $fn
     *
     * @return $this
     */
    public function extend(string $name, Closure $fn)
    {
        $this->parsers[$name] = $fn;

        return $this;
    }

    /**
     * Add a new parser to the beginning of the list.
     *
     * @param string $name
     * @param Closure $fn
     *
     * @return $this
     */
    public function unshift(string $name, Closure $fn)
    {
        $this->parsers = [$name => $fn] + $this->parsers;

        return $this;
    }

    /**
     * Remove the specified parser if it is defined.
     *
     * @param string $name
     *
     * @return $this
     */
    public function remove(string $name)
    {
        unset($this->parsers[$name]);

        return $this;
    }

    /**
     * Remove all registered parsers.
     *
     * @return $this
     */
    public function clear()
    {
        $this->parsers = [];

        return $this;
    }

    /**
     * Runs the provided request through all the parsers, and returns the first job provided by any of the parsers.
     *
     * @param Request $request
     * @return HttpJob|null
     */
    public function capture(Request $request): ?HttpJob
    {
        foreach ($this->parsers as $name => $fn) {
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
