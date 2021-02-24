<?php

namespace Garbetjie\Laravel\HttpQueueWorker;

use Garbetjie\Laravel\HttpQueueWorker\Events\JobNotCaptured;
use Garbetjie\Laravel\HttpQueueWorker\Exception\NotAuthenticatedException;
use Garbetjie\Laravel\HttpQueueWorker\Exception\NotCapturedException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Queue\Worker;
use Illuminate\Queue\WorkerOptions;
use Illuminate\Contracts\Queue\Job as JobContract;
use Throwable;
use function app;
use function event;
use function response;

class HttpQueueController
{
    /**
     * @param Request $request
     * @throws NotCapturedException
     * @return mixed|Response
     */
    public function handle(Request $request)
    {
        if (!$job = $this->captureJob($request)) {
            return $this->handleUncaptured($request);
        }

        try {
            $this->getQueueWorker()->process(null, $job, $this->getWorkerOptions());
        } catch (Throwable $e) {
            $this->handleProcessingException($e);
        }

        return response('', Response::HTTP_NO_CONTENT);
    }

    protected function handleProcessingException(Throwable $e)
    {
        // Intentionally left empty, so that requests are not retried.
        // The queue worker is responsible for releasing the job back onto the queue.
    }

    /**
     * @param Request $request
     * @throws NotCapturedException
     *
     * @return mixed
     */
    protected function handleUncaptured(Request $request)
    {
        throw new NotCapturedException($request);
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
