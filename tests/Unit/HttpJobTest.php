<?php

namespace Tests\Unit;

use Garbetjie\Laravel\HttpQueueWorker\HttpJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Queue;
use Tests\Jobs\DoesItRunJob;
use Tests\MakesJobPayload;
use Tests\TestCase;

class HttpJobTest extends TestCase
{
    use MakesJobPayload;

    public function testJobIdIsPopulatedCorrectly()
    {
        $job = new HttpJob($this->app, fn() => null, new Request(), 'default', 'id', 'body', 0);

        $this->assertSame('id', $job->getJobId());
    }

    public function testRawBodyIsPopulatedCorrectly()
    {
        $payload = $this->makePayloadString(new DoesItRunJob());
        $job = new HttpJob($this->app, fn() => null, new Request(), 'default', 'id', $payload, 0);

        $this->assertSame($payload, $job->getRawBody());
    }

    public function testAttemptsArePopulatedCorrectly()
    {
        $job = new HttpJob($this->app, fn() => null, new Request(), 'default', 'id', 'body', 2425);

        $this->assertSame(2425, $job->attempts());
    }

    public function testReleasingWorksSuccessfully()
    {
        $queue = Queue::fake();

        $job = new HttpJob($this->app, fn() => null, new Request(), 'default', 'id', $this->makePayloadString(new DoesItRunJob()), 0);
        $job->fire();

        $this->assertCount(0, $queue->pushedJobs());
        $job->release();
        $this->assertCount(1, $queue->pushedJobs());
    }
}
