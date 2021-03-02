<?php

namespace Tests\Unit;

use Garbetjie\Laravel\HttpQueueWorker\ConnectionResolver;
use Garbetjie\Laravel\HttpQueueWorker\HttpQueueManager;
use Garbetjie\Laravel\HttpQueueWorker\Parsers\GoogleCloudTasksParser;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use ReflectionObject;
use Tests\TestCase;

class ServiceProviderTest extends TestCase
{
    public function testManagerAliasIsRegistered()
    {
        $this->assertInstanceOf(HttpQueueManager::class, $this->app->get('httpQueue'));
    }

    public function testManagerClassIsRegistered()
    {
        $this->assertInstanceOf(HttpQueueManager::class, $this->app->get(HttpQueueManager::class));
    }

    public function testManagerClassAndAliasAreSame()
    {
        $this->assertSame(
            $this->app->get('httpQueue'),
            $this->app->get(HttpQueueManager::class)
        );
    }

    public function testDefaultParsersAreRegistered()
    {
        $parsers = $this->getParsersFromManager();

        foreach (['google-cloud-tasks'] as $parser) {
            $this->assertArrayHasKey($parser, $parsers);
        }
    }

    protected function getParsersFromManager()
    {
        $manager = $this->app->get(HttpQueueManager::class);

        $prop = (new ReflectionObject($manager))->getProperty('parsers');
        $prop->setAccessible(true);

        return $prop->getValue($manager);
    }
}
