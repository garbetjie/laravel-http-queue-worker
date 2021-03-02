<?php

namespace Tests\Feature;

use Garbetjie\Laravel\HttpQueueWorker\HttpQueueController;
use Illuminate\Queue\CallQueuedHandler;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Testing\Fakes\QueueFake;
use Orchestra\Testbench\Concerns\CreatesApplication;
use ReflectionObject;
use Tests\Jobs\FailExceptionallyJob;
use Tests\Jobs\FailManuallyJob;
use Tests\Jobs\ReleaseJob;
use Tests\MakesParserRequests;
use Tests\MakesJobPayload;
use Tests\Jobs\RunJob;
use Tests\TestCase;
use function collect;
use function config;
use function get_class_methods;

class HttpQueueControllerTest extends TestCase
{
    use MakesJobPayload, MakesParserRequests;

    protected QueueFake $queue;

    protected function setUp(): void
    {
        parent::setUp();

        $this->queue = Queue::fake();

        Route::post('/', [HttpQueueController::class, 'handle']);
    }

    /**
     * @dataProvider successfulRequestDataProvider
     *
     * @param array $headers
     */
    public function testStatusCodeIs204WhenJobParsedSuccessfully(array $headers)
    {
        $response = $this->postJson('/', $this->makePayload(new RunJob()), $headers);
        $response->assertStatus(204);
    }

    /**
     * @dataProvider failedRequestDataProvider
     *
     * @param array $headers
     */
    public function testStatusCodeIs400WhenJobFailsToParse(array $headers)
    {
        $response = $this->postJson('/', $this->makePayload(new RunJob()), $headers);
        $response->assertStatus(400);
    }

    /**
     * @dataProvider successfulRequestDataProvider
     *
     * @param array $headers
     */
    public function testStatusCodeIs204WhenJobRunsSuccessfully(array $headers)
    {
        $response = $this->postJson('/', $this->makePayload(new RunJob()), $headers);
        $response->assertStatus(204);
    }

    /**
     * @dataProvider successfulRequestDataProvider
     *
     * @param array $headers
     */
    public function testJobActuallyRuns(array $headers)
    {
        RunJob::$ran = false;

        $this->postJson('/', $this->makePayload(new RunJob()), $headers);

        $this->assertTrue(RunJob::$ran);
    }

    /**
     * @dataProvider successfulRequestDataProvider
     *
     * @param array $headers
     */
    public function testQueueIsEmptyWhenJobRunsSuccessfully(array $headers)
    {
        $this->queue->assertNothingPushed();

        $this->postJson('/', $this->makePayload(new RunJob()), $headers);

        $this->queue->assertNothingPushed();
    }

    /**
     * @dataProvider successfulRequestDataProvider
     *
     * @param array $headers
     */
    public function testStatusCodeIs204WhenJobFailedManually(array $headers)
    {
        $response = $this->postJson('/', $this->makePayload(new FailManuallyJob()), $headers);
        $response->assertStatus(204);
    }

    /**
     * @dataProvider successfulRequestDataProvider
     *
     * @param array $headers
     */
    public function testQueueIsEmptyWhenJobFailedManually(array $headers)
    {
        $this->queue->assertNothingPushed();

        $this->postJson('/', $this->makePayload(new FailManuallyJob()), $headers);

        $this->queue->assertNothingPushed();
    }

    /**
     * @dataProvider successfulRequestDataProvider
     *
     * @param array $headers
     */
    public function testStatusCodeIs500WhenJobFailsWithException(array $headers)
    {
        $response = $this->postJson('/', $this->makePayload(new FailExceptionallyJob()), $headers);
        $response->assertStatus(500);
    }

    /**
     * @dataProvider successfulRequestDataProvider
     *
     * @param array $headers
     */
    public function testQueueIsEmptyWhenJobFailedWithException(array $headers)
    {
        $this->queue->assertNothingPushed();

        $this->postJson('/', $this->makePayload(new FailExceptionallyJob()), $headers);

        $this->queue->assertNothingPushed();
    }

    /**
     * @dataProvider maxAttemptDataProvider
     *
     * @param array $headers
     */
    public function testStatusCodeIs204WhenMaxAttemptsAreReached(array $headers)
    {
        $response = $this->postJson('/', $this->makePayload(new FailExceptionallyJob()), $headers);
        $response->assertStatus(204);
    }

    /**
     * @dataProvider maxAttemptDataProvider
     *
     * @param array $headers
     */
    public function testQueueIsEmptyWhenMaxAttemptsAreReach(array $headers)
    {
        $this->queue->assertNothingPushed();

        $this->postJson('/', $this->makePayload(new FailExceptionallyJob()), $headers);

        $this->queue->assertNothingPushed();
    }

    public function successfulRequestDataProvider(): array
    {
        return [
            'google cloud tasks' => [
                $this->makeCloudTasksRequestHeaders(),
            ],
        ];
    }

    public function maxAttemptDataProvider(): array
    {
        return [
            'google cloud tasks' => [
                $this->makeCloudTasksRequestHeaders(4)
            ]
        ];
    }

    public function failedRequestDataProvider(): array
    {
        return [
            [
                ['User-Agent' => (string)Str::uuid()],
            ],
        ];
    }
}
