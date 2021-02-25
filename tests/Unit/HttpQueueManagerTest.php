<?php

namespace Tests\Unit;

use Garbetjie\Laravel\HttpQueueWorker\HttpJob;
use Garbetjie\Laravel\HttpQueueWorker\HttpQueueManager;
use Garbetjie\Laravel\HttpQueueWorker\Parsers\CloudTasksParser;
use Illuminate\Http\Request;
use ReflectionObject;
use Tests\MakesParserRequests;
use Tests\TestCase;
use function reset;

class HttpQueueManagerTest extends TestCase
{
    use MakesParserRequests;

    public function testParserCanBeAddedToEnd()
    {
        $manager = new HttpQueueManager($this->app);
        $manager->extend('my parser', fn() => null);

        $parsers = $this->getParsersFromManager($manager);

        $this->assertCount(1, $parsers);
    }

    public function testParserCanBeAddedToBeginning()
    {
        $manager = new HttpQueueManager($this->app);
        $manager->extend('parser2', fn() => null);
        $manager->unshift('parser1', fn() => null);

        $parsers = $this->getParsersFromManager($manager);

        $this->assertCount(2, $parsers);

        reset($parsers);
        $this->assertSame('parser1', array_keys($parsers)[0]);
    }

    public function testParserCanBeRemoved()
    {
        $manager = new HttpQueueManager($this->app);
        $manager->extend('parser', fn() => null);

        $this->assertCount(1, $this->getParsersFromManager($manager));

        $manager->remove('parser');

        $this->assertCount(0, $this->getParsersFromManager($manager));
    }

    public function testJobCanBeResolved()
    {
        $manager = new HttpQueueManager($this->app);
        $manager->extend('google cloud tasks', fn() => new CloudTasksParser($this->app));

        $request = new Request();
        $request->headers->add($this->makeCloudTasksRequestHeaders());

        $this->assertInstanceOf(HttpJob::class, $manager->capture($request));
    }

    protected function getParsersFromManager(HttpQueueManager $manager)
    {
        $prop = (new ReflectionObject($manager))->getProperty('parsers');
        $prop->setAccessible(true);

        return $prop->getValue($manager);
    }
}
