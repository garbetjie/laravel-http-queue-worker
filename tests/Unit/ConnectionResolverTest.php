<?php

namespace Tests\Unit;

use Garbetjie\Laravel\HttpQueueWorker\ConnectionResolver;
use Tests\TestCase;

class ConnectionResolverTest extends TestCase
{
    public function testIsCallable()
    {
        $this->assertIsCallable(new ConnectionResolver());
    }
}
