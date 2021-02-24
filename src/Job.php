<?php

namespace Garbetjie\Laravel\HttpQueueWorker;

use Closure;
use Illuminate\Contracts\Container\Container;
use Illuminate\Queue\Jobs\Job as BaseJob;
use Illuminate\Contracts\Queue\Job as JobContract;
use Illuminate\Queue\QueueManager;
use function app;
use function is_string;
use function value;

class Job extends BaseJob implements JobContract
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
     * @var string|null
     */
    protected ?string $connection = null;

    public function __construct(
        Container $container,
        string $id,
        string $body,
        int $attempts
    ) {
        $this->container = $container;
        $this->id = $id;
        $this->body = $body;
        $this->attempts = $attempts;
    }

    public function getJobId()
    {
        return $this->id;
    }

    public function getRawBody()
    {
        return $this->body;
    }

    public function attempts()
    {
        return $this->attempts;
    }

    public function release($delay = 0)
    {
        parent::release($delay);

        $this->container->get(QueueManager::class)->connection(
            $this->connection
        )->later(
            $delay,
            $this->getResolvedJob(),
            $this->getQueue()
        );
    }

    /**
     * Set the connection on which the job will be run.
     *
     * @param string|callable $connection
     *
     * @return Job
     */
    public function onConnection($connection): Job
    {
        $this->connection = is_string($connection)
            ? $connection
            : $this->container->call($connection, [$this]);

        return $this;
    }

    /**
     * Set the queue on which this job is being run.
     *
     * @param string|callable $queue
     * @return Job
     */
    public function onQueue($queue): Job
    {
        $this->queue = is_string($queue)
            ? $queue
            : $this->container->call($queue, [$this]);

        return $this;
    }
}
