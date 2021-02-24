<?php

namespace Tests\Unit;

use Garbetjie\Laravel\HttpQueueWorker\Job;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class JobTest extends TestCase
{
    public function testJobIdIsPopulatedCorrectly()
    {
        $job = new Job($this->app, 'id', 'body', 0);

        $this->assertSame('id', $job->getJobId());
    }

    public function testRawBodyIsPopulatedCorrectly()
    {
        $job = new Job($this->app, 'id', 'body', 0);

        $this->assertSame('body', $job->getRawBody());
    }

    public function testAttemptsArePopulatedCorrectly()
    {
        $job = new Job($this->app, 'id', 'body', 2425);

        $this->assertSame(2425, $job->attempts());
    }

    public function testReleasingWorksSuccessfully()
    {
        $queue = Queue::fake();

        $job = new Job($this->app, 'id', 'body', 0);

        $this->assertCount(0, $queue->pushedJobs());
        $job->release();
        $this->assertCount(1, $queue->pushedJobs());
    }

    public function testConnectionIsSetCorrectly()
    {

    }

    public function testQueueIsSetCorrectly()
    {

    }
}
