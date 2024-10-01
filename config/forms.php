<?php

return [
    'fill-day-hash' => filter_var(env('RUN_FILL_DAY_HASH_FORMS_COMMAND', false), FILTER_VALIDATE_BOOLEAN),
    'fill-day-hash-chunk-size' => env('FILL_DAY_HASH_COMMAND_CHUNK', 50000),
];
