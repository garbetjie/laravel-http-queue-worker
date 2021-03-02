<?php

namespace Tests\Unit;

use Garbetjie\Laravel\HttpQueueWorker\HttpJob;
use Illuminate\Http\Request;
use Illuminate\Queue\CallQueuedHandler;
use Illuminate\Support\Facades\Queue;
use Tests\Jobs\FailManuallyJob;
use Tests\Jobs\RunJob;
use Tests\MakesJobPayload;
use Tests\TestCase;
use function get_class;

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
        $payload = $this->makePayloadString(new RunJob());
        $job = new HttpJob($this->app, fn() => null, new Request(), 'default', 'id', $payload, 0);

        $this->assertSame($payload, $job->getRawBody());
    }

    public function testAttemptsArePopulatedCorrectly()
    {
        $job = new HttpJob($this->app, fn() => null, new Request(), 'default', 'id', 'body', 2425);

        $this->assertSame(2425, $job->attempts());
    }
}
