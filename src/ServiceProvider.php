<?php

namespace Garbetjie\Laravel\HttpQueueWorker;

use Garbetjie\Laravel\HttpQueueWorker\Parsers\CloudTasksParser;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use function config;

class ServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        Route::post('/', [HttpQueueController::class, 'handle']);
    }

    public function bootRoute()
    {

    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config.php', 'httpqueue');

        $this->registerManager();
        $this->registerConnectionResolver();
    }

    protected function registerConnectionResolver()
    {
        $this->app->bind('httpQueue.connectionResolver', ConnectionResolver::class);
    }

    protected function registerManager()
    {
        // Register the HTTP queue manager.
        $this->app->singleton('httpQueue', function($app) {
            return tap(new HttpQueueManager($app), function($manager) {
                if (config('httpqueue.register_default_parsers')) {
                    $this->registerParsers($manager);
                }
            });
        });

        // Register the manager alias as the class name.
        $this->app->alias('httpQueue', HttpQueueManager::class);
    }

    protected function registerParsers(HttpQueueManager $manager)
    {
        $manager->extend('google-cloud-tasks', fn() => new CloudTasksParser($this->app));
    }
}
