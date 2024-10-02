<?php

return [
    'fix' => filter_var(env('RUN_FIX_FORMS_COMMAND', false), FILTER_VALIDATE_BOOLEAN),
    'fix-chunk-size' => env('FIX_FORMS_COMMAND_CHUNK', 30000),
    'transfer' => filter_var(env('RUN_TRANSFER_FORMS_COMMAND', false), FILTER_VALIDATE_BOOLEAN),
    'transfer-chunk-size' => env('TRANSFER_FORMS_COMMAND_CHUNK', 15000),
];
