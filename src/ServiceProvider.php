<?php

namespace Garbetjie\Laravel\HttpQueueWorker;

use Garbetjie\Laravel\HttpQueueWorker\Handler\CloudTasks;
use Illuminate\Http\Request;
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
        $this->mergeConfigFrom(__DIR__ . '/../config.php', 'http-queue-worker');

        $this->registerManager();
        $this->registerConnectionResolver();
    }

    protected function registerConnectionResolver()
    {
        $this->app->bind('httpQueue.connectionResolver', function() {

        });
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
        foreach (['GoogleCloudTasks'] as $parser) {
            $this->{"register{$parser}Parser"}($manager);
        }
    }

    protected function registerGoogleCloudTasksParser(HttpQueueManager $manager)
    {
        $manager->extend('googleCloudTasks', function() {
            return new CloudTasks($this->app);
        });
    }
}
