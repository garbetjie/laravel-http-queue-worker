<?php

namespace Tests\Unit\Parsers;

use Garbetjie\Laravel\HttpQueueWorker\Parsers\CloudTasksParser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Tests\MakesParserRequests;
use Tests\TestCase;

class CloudTasksParserTest extends TestCase
{
    use MakesParserRequests;

    public function testJobIsParsedSuccessfully()
    {
        $request = new Request();
        $request->headers->add($this->makeCloudTasksRequestHeaders());

        $parser = new CloudTasksParser($this->app);

        $this->assertNotNull($parser($request));
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

        $parser = new CloudTasksParser($this->app);

        $this->assertNull($parser($request));
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
