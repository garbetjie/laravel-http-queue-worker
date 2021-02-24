<?php

namespace Garbetjie\Laravel\HttpQueueWorker\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Throwable;

/**
 * @method static dispatch(Throwable $cause)
 */
class JobNotCaptured
{
    use Dispatchable;

    /**
     * @var Throwable
     */
    protected $cause;

    /**
     * @param Throwable $cause
     */
    public function __construct(Throwable $cause)
    {
        $this->cause = $cause;
    }

    /**
     * @return Throwable
     */
    public function getCause()
    {
        return $this->cause;
    }
}
