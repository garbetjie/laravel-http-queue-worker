<?php

namespace Garbetjie\Laravel\HttpQueueWorker\Handler;

use Closure;
use function value;

trait PopulatesQueueName
{
    /**
     * @var string|null
     */
    protected $queueName;

    /**
     * @return string|null
     */
    public function getQueueName(): ?string
    {
        return $this->queueName;
    }

    /**
     * @param string|Closure|null $queueName
     * @return $this
     */
    public function setQueueName($queueName)
    {
        $this->queueName = value($queueName);

        return $this;
    }
}
