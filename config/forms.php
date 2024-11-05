<?php

return [
    'fill-day-hash' => filter_var(env('RUN_FILL_DAY_HASH_FORMS_COMMAND', false), FILTER_VALIDATE_BOOLEAN),
    'fill-day-hash-chunk-size' => env('FILL_DAY_HASH_COMMAND_CHUNK', 50000),
    'fix' => filter_var(env('RUN_FIX_FORMS_COMMAND', false), FILTER_VALIDATE_BOOLEAN),
    'fix-chunk-size' => env('FIX_FORMS_COMMAND_CHUNK', 30000),
    'transfer' => filter_var(env('RUN_TRANSFER_FORMS_COMMAND', false), FILTER_VALIDATE_BOOLEAN),
    'transfer-chunk-size' => env('TRANSFER_FORMS_COMMAND_CHUNK', 15000),
    'restore-foreign' => filter_var(env('RUN_RESTORE_FOREIGN_FORMS_COMMAND', false), FILTER_VALIDATE_BOOLEAN),
    'restore-foreign-chunk-size' => env('RESTORE_FOREIGN_FORMS_COMMAND_CHUNK', 15000),
];
