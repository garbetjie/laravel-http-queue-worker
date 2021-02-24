<?php

use Garbetjie\Laravel\HttpQueueWorker\Handler\CloudTasks;

return [
    'auth' => true,

    'register_default_parsers' => true,

    // Connections to be used by each task handler in order to release jobs back onto the queue.
    'connections' => [
        'googleCloudTasks' => 'cloud-tasks',
    ],

    'parsers2' => [
        CloudTasks::class,
        function() {

        },
        'myKey' => CloudTasks::class,
        'myKey2' => [
            'className' => CloudTasks::class,
            'connectionName' => null,
        ]
    ],

    'parsers' => [
        'myParser' => [
            'className' => CloudTasks::class,
            'connectionName' => 'cloud-tasks',
        ],
        'googleCloudTasks',
        'googlePubSub',
        '\\App\\Parser\\MyCustomParser'
    ]
];
