<?php

return [
    'log' => env('WEBHOOK_LOG', false),
    'retry' => env('WEBHOOK_RETRY', 0),
    'timeout' => env('WEBHOOK_TIMEOUT', 5),
];