<?php

namespace Tests\Unit;

use Garbetjie\Laravel\HttpQueueWorker\HttpJob;
use Garbetjie\Laravel\HttpQueueWorker\HttpQueueManager;
use Garbetjie\Laravel\HttpQueueWorker\Parsers\CloudTasksParser;
use Illuminate\Http\Request;
use ReflectionObject;
use Tests\MakesHandlerRequests;
use Tests\TestCase;
use function reset;

class HttpQueueManagerTest extends TestCase
{
    use MakesHandlerRequests;

    public function testParserCanBeAddedToEnd()
    {
        $manager = new HttpQueueManager($this->app);
        $manager->extend('my parser', fn() => null);

        $handlers = $this->getHandlersFromManager($manager);

        $this->assertCount(1, $handlers);
    }

    public function testParserCanBeAddedToBeginning()
    {
        $manager = new HttpQueueManager($this->app);
        $manager->extend('parser2', fn() => null);
        $manager->unshift('parser1', fn() => null);

        $parsers = $this->getHandlersFromManager($manager);

        $this->assertCount(2, $parsers);

        reset($parsers);
        $this->assertSame('parser1', array_keys($parsers)[0]);
    }

    public function testParserCanBeRemoved()
    {
        $manager = new HttpQueueManager($this->app);
        $manager->extend('parser', fn() => null);

        $this->assertCount(1, $this->getHandlersFromManager($manager));

        $manager->remove('parser');

        $this->assertCount(0, $this->getHandlersFromManager($manager));
    }

    public function testJobCanBeResolved()
    {
        $manager = new HttpQueueManager($this->app);
        $manager->extend('google cloud tasks', fn() => new CloudTasksParser($this->app));

        $request = new Request();
        $request->headers->add($this->makeCloudTasksRequestHeaders());

        $this->assertInstanceOf(HttpJob::class, $manager->capture($request));
    }

    protected function getHandlersFromManager(HttpQueueManager $manager)
    {
        $prop = (new ReflectionObject($manager))->getProperty('handlers');
        $prop->setAccessible(true);

        return $prop->getValue($manager);
    }
}
