<?php

namespace Garbetjie\Laravel\HttpQueueWorker\Exception;

use Exception;
use Illuminate\Http\Request;
use Throwable;

class NotCapturedException extends Exception
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request;

        parent::__construct('No handler found to capture job from request');
    }

    public function getRequest()
    {
        return $this->request;
    }
}
