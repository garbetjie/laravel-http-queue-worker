<?php

namespace Garbetjie\Laravel\HttpQueueWorker;

use Closure;
use Illuminate\Contracts\Queue\Job as JobContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void extend(string $name, Closure $handler)
 * @method static void unshift(string $name, Closure $handler)
 * @method static void remove(string $name)
 * @method static JobContract|null capture(Request $request)
 *
 * @mixin HttpQueueManager
 */
class HttpQueue extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'httpQueue';
    }
}
