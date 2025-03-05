<?php

return [
    'bot_token' => env('TG_BOT_TOKEN'),

    'chats' => [
        'dismissed' => env('TG_CHATS_DISMISSED'),
        //TODO: мб по модулям разнести
        'employee-dismissed' => env('TG_CHATS_EMPLOYEE_DISMISSED')
    ]
];
