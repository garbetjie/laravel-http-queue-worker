<?php

namespace Tests;

use Illuminate\Support\Str;

trait MakesParserRequests
{
    protected function makeCloudTasksRequestHeaders(): array
    {
        return [
            'User-Agent' => 'Google-Cloud-Tasks',
            'X-Cloudtasks-Taskname' => (string)Str::uuid(),
            'X-Cloudtasks-Queuename' => 'default',
        ];
    }
}
