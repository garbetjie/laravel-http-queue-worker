<?php

namespace Garbetjie\Laravel\HttpQueueWorker;

use Garbetjie\Laravel\HttpQueueWorker\Parsers\GoogleCloudTasksParser;
use Illuminate\Routing\Router;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use function config;

class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        // void
    }

    public function register()
    {
        $this->registerManager();
    }

    protected function registerManager()
    {
        // Register the HTTP queue manager.
        $this->app->singleton('httpQueue', function($app) {
            return tap(new HttpQueueManager($app), function($manager) {
                $this->registerParsers($manager);
            });
        });

        // Register the manager alias as the class name.
        $this->app->alias('httpQueue', HttpQueueManager::class);
    }

    protected function registerParsers(HttpQueueManager $manager)
    {
        $manager->extend('google-cloud-tasks', fn() => new GoogleCloudTasksParser($this->app));
    }
}
