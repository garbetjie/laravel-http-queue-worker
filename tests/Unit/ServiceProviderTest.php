<?php

namespace Tests\Unit;

use Garbetjie\Laravel\HttpQueueWorker\ConnectionResolver;
use Garbetjie\Laravel\HttpQueueWorker\HttpQueueManager;
use Garbetjie\Laravel\HttpQueueWorker\Parsers\CloudTasksParser;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Config;
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

    /**
     * @environment-setup enableDefaultParsers
     */
    public function testDefaultParsersAreRegistered()
    {
        $parsers = $this->getParsersFromManager();

        foreach (['google-cloud-tasks'] as $parser) {
            $this->assertArrayHasKey($parser, $parsers);
        }
    }

    public function testConnectionResolverIsRegistered()
    {
        $this->assertInstanceOf(ConnectionResolver::class, $this->app->get('httpQueue.connectionResolver'));
    }

    public function testDefaultConfigStructure()
    {
        $config = $this->app['config']->get('httpqueue');

        $this->assertIsArray($config);

        $this->assertArrayHasKey('register_default_parsers', $config);
        $this->assertIsBool($config['register_default_parsers']);
    }

    /**
     * @environment-setup disableDefaultParsers
     */
    public function testDefaultParsersAreNotRegistered()
    {
        $this->assertCount(0, $this->getParsersFromManager());
    }

    protected function disableDefaultParsers($app)
    {
        $app['config']->set('httpqueue.register_default_parsers', false);
    }

    protected function enableDefaultParsers($app)
    {
        $app['config']->set('httpqueue.register_default_parsers', true);
    }

    protected function getParsersFromManager()
    {
        $manager = $this->app->get(HttpQueueManager::class);

        $prop = (new ReflectionObject($manager))->getProperty('parsers');
        $prop->setAccessible(true);

        return $prop->getValue($manager);
    }
}
