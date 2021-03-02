<?php

namespace Garbetjie\Laravel\HttpQueueWorker\Parsers;

use Garbetjie\Laravel\HttpQueueWorker\HttpJob;
use Illuminate\Http\Request;

class GoogleCloudTasksParser extends Parser
{
    public function __invoke(Request $request): ?HttpJob
    {
        if ($request->userAgent() !== 'Google-Cloud-Tasks') {
            return null;
        }

        if (!$request->hasHeader('X-Cloudtasks-Taskname') || !$request->hasHeader('X-Cloudtasks-Queuename')) {
            return null;
        }

        return new HttpJob(
            $this->container,
            $this,
            $request,
            $request->header('X-Cloudtasks-Taskname'),
            $request->header('X-Cloudtasks-Queuename'),
            $request->getContent(),
            (int)$request->header('X-CloudTasks-TaskRetryCount', 0),
        );
    }
}
