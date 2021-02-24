<?php

namespace Tests\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DoesItRunJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected static bool $ran = false;

    public function handle()
    {
        static::$ran = true;
    }

    public static function reset()
    {
        static::$ran = false;
    }

    public static function ran(): bool
    {
        return static::$ran;
    }

}
