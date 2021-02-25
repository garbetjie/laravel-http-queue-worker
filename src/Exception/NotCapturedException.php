<?php

namespace Garbetjie\Laravel\HttpQueueWorker\Exception;

use Exception;
use Illuminate\Http\Request;
use Throwable;

class NotCapturedException extends Exception
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;

        parent::__construct('No parser found to capture job from request');
    }

    public function request(): Request
    {
        return $this->request;
    }
}
