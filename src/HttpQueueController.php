<?php

namespace Garbetjie\Laravel\HttpQueueWorker;

use Garbetjie\Laravel\HttpQueueWorker\Events\JobNotCaptured;
use Garbetjie\Laravel\HttpQueueWorker\Exception\NotAuthenticatedException;
use Garbetjie\Laravel\HttpQueueWorker\Exception\NotCapturedException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Queue\ManuallyFailedException;
use Illuminate\Queue\MaxAttemptsExceededException;
use Illuminate\Queue\Worker;
use Illuminate\Queue\WorkerOptions;
use Illuminate\Contracts\Queue\Job as JobContract;
use Illuminate\Support\Facades\Config;
use Throwable;
use function app;
use function event;
use function response;

class HttpQueueController
{
    /**
     * @param Request $request
     *
     * @return mixed|Response
     */
    public function handle(Request $request)
    {
        if (!$job = $this->captureJob($request)) {
            return response('', Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->getQueueWorker()->process($job->getConnectionName(), $job, $this->getWorkerOptions());
        } catch (MaxAttemptsExceededException $e) {
            // void
        } catch (Throwable $e) {
            return response('', Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response('', Response::HTTP_NO_CONTENT);
    }

    /**
     * @return Worker
     */
    protected function getQueueWorker(): Worker
    {
        return app('queue.worker');
    }

    /**
     * @param Request $request
     * @return JobContract|null
     */
    protected function captureJob(Request $request): ?JobContract
    {
        return HttpQueue::capture($request);
    }

    /**
     * @return WorkerOptions
     */
    protected function getWorkerOptions(): WorkerOptions
    {
        return new WorkerOptions();
    }
}
