<?php

namespace Garbetjie\Laravel\HttpQueueWorker\Handler;

use Closure;
use function value;

trait PopulatesConnectionName
{
    /**
     * @var string|Closure|null
     */
    protected $connectionName = null;

    /**
     * @return string|null
     */
    public function getConnectionName(): ?string
    {
        return $this->connectionName;
    }

    /**
     * @param string|Closure|null $connectionName
     *
     * @return $this
     */
    public function setConnectionName($connectionName)
    {
        $this->connectionName = value($connectionName);

        return $this;
    }
}
