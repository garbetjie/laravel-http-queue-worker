<?php

namespace Tests;

use Garbetjie\Laravel\HttpQueueWorker\ServiceProvider;
use Illuminate\Contracts\Http\Kernel as HttpKernelContract;
use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Foundation\Application;
use Orchestra\Testbench\TestCase as BaseTestCase;
use function dirname;

abstract class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [ServiceProvider::class];
    }
}
