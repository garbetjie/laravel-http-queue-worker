<?php

namespace Garbetjie\Laravel\HttpQueueWorker;

use Closure;
use Illuminate\Contracts\Container\Container;
use Illuminate\Http\Request;
use Illuminate\Queue\Jobs\Job as BaseJob;
use Illuminate\Contracts\Queue\Job as JobContract;
use Illuminate\Queue\QueueManager;
use function app;
use function is_string;
use function value;

class HttpJob extends BaseJob implements JobContract
{
    /**
     * @var string|null
     */
    protected ?string $id = null;

    /**
     * @var string|null
     */
    protected ?string $body = null;

    /**
     * @var int
     */
    protected int $attempts = 0;

    /**
     * @var Request
     */
    protected Request $request;

    /**
     * @var callable
     */
    protected $parser;

    public function __construct(
        Container $container,
        callable $parser,
        Request $request,
        ?string $queue,
        string $id,
        string $body,
        int $attempts
    ) {
        $this->parser = $parser;
        $this->container = $container;
        $this->id = $id;
        $this->body = $body;
        $this->queue = $queue;
        $this->attempts = $attempts;
        $this->request = $request;
    }

    /**
     * Return the request instance from which the job was parsed.
     *
     * @return Request
     */
    public function request(): Request
    {
        return $this->request;
    }

    /**
     * Returns the parser used to create the job.
     *
     * @return callable
     */
    public function parser(): callable
    {
        return $this->parser;
    }

    /**
     * @return string|null
     */
    public function getJobId()
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getRawBody()
    {
        return $this->body;
    }

    /**
     * @return int
     */
    public function attempts()
    {
        return $this->attempts;
    }

    /**
     * @param int $delay
     */
    public function release($delay = 0)
    {
        // void
    }

    public function getConnectionName()
    {
        return $this->connectionName;
    }
}
