<?php

namespace Tests\Feature;

use Illuminate\Support\Str;
use Tests\MakesHandlerRequests;
use Tests\MakesJobPayload;
use Tests\Jobs\DoesItRunJob;
use Tests\TestCase;

class HttpQueueControllerTest extends TestCase
{
    use MakesJobPayload, MakesHandlerRequests;

    /**
     * @dataProvider successfulRequestDataProvider
     *
     * @param array $headers
     */
    public function testRequestParsesSuccessfully(array $headers)
    {
        $this->postJson('/', $this->makePayload(new DoesItRunJob()), $headers)->assertStatus(204);
    }

    /**
     * @dataProvider successfulRequestDataProvider
     *
     * @param array $headers
     */
    public function testJobRunsSuccessfully(array $headers)
    {
        // Reset the job.
        DoesItRunJob::$ran = false;

        // Parse & run the job.
        $this->postJson('/', $this->makePayload(new DoesItRunJob()), $headers);

        // Ensure it ran.
        $this->assertTrue(DoesItRunJob::$ran);
    }

    public function successfulRequestDataProvider(): array
    {
        return [
            'google cloud tasks' => [
                $this->makeCloudTasksRequestHeaders()
            ]
        ];
    }

    /**
     * @dataProvider failedRequestDataProvider
     *
     * @param array $headers
     */
    public function testRequestFailsToParse(array $headers)
    {
        $this->postJson('/', $this->makePayload(new DoesItRunJob()), $headers)->assertStatus(500);
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
