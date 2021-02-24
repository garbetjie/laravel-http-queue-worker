<?php

namespace Tests;

use Illuminate\Support\Str;

trait MakesHandlerRequests
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
