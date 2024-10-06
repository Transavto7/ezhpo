<?php

return [
    'fill-day-hash' => filter_var(env('RUN_FILL_DAY_HASH_FORMS_COMMAND', false), FILTER_VALIDATE_BOOLEAN),
    'fill-day-hash-chunk-size' => env('FILL_DAY_HASH_COMMAND_CHUNK', 50000),
    'fix' => filter_var(env('RUN_FIX_FORMS_COMMAND', false), FILTER_VALIDATE_BOOLEAN),
    'fix-chunk-size' => env('FIX_FORMS_COMMAND_CHUNK', 30000),
];
