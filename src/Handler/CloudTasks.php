<?php

namespace Garbetjie\Laravel\HttpQueueWorker\Handler;

use Garbetjie\Laravel\HttpQueueWorker\Job;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use function app;
use function get_class;
use function spl_object_id;

class CloudTasks
{
    use PopulatesConnectionName, PopulatesQueueName, ResolvesConnectionNames;

    protected Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function __invoke(Request $request): ?Job
    {
        if ($request->userAgent() !== 'Google-Cloud-Tasks') {
            return null;
        }

        if (!$request->hasHeader('X-Cloudtasks-Taskname') || !$request->hasHeader('X-Cloudtasks-Queuename')) {
            return null;
        }

        return (new Job(
            $this->container,
            $request->header('X-Cloudtasks-Taskname'),
            $request->getContent(),
            (int)$request->header('X-CloudTasks-TaskRetryCount', 0),
        ))->onQueue(
            $request->header('X-Cloudtasks-Queuename')
        );
    }
}
