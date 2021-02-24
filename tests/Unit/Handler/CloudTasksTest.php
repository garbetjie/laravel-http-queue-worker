<?php

namespace Tests\Unit\Handler;

use Garbetjie\Laravel\HttpQueueWorker\Handler\CloudTasks;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tests\MakesHandlerRequests;
use Tests\TestCase;

class CloudTasksTest extends TestCase
{
    use WithFaker;
    use MakesHandlerRequests;

    public function testJobIsParsedSuccessfully()
    {
        $request = new Request();
        $request->headers->add($this->makeCloudTasksRequestHeaders());

        $handler = new CloudTasks($this->app);

        $this->assertNotNull($handler($request));
    }

    /**
     * @dataProvider parseFailureDataProvider
     *
     * @param array $headers
     */
    public function testJobFailsToParse(array $headers)
    {
        $request = new Request();
        $request->headers->add($headers);

        $handler = new CloudTasks($this->app);

        $this->assertNull($handler($request));
    }

    public function parseFailureDataProvider(): array
    {
        return [
            [[
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4 rv:5.0; sl-SI) AppleWebKit/535.1.7 (KHTML, like Gecko) Version/5.1 Safari/535.1.7',
            ]],
            [[
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4 rv:5.0; sl-SI) AppleWebKit/535.1.7 (KHTML, like Gecko) Version/5.1 Safari/535.1.7',
                'X-Cloudtasks-Taskname' => (string)Str::uuid(),
            ]],
            [[
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4 rv:5.0; sl-SI) AppleWebKit/535.1.7 (KHTML, like Gecko) Version/5.1 Safari/535.1.7',
                'X-Cloudtasks-Queuename' => 'default',
            ]],
            [[
                'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_4 rv:5.0; sl-SI) AppleWebKit/535.1.7 (KHTML, like Gecko) Version/5.1 Safari/535.1.7',
                'X-Cloudtasks-Queuename' => 'default',
                'X-Cloudtasks-Taskname' => (string)Str::uuid(),
            ]]
        ];
    }
}