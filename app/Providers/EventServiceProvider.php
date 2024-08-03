<?php

namespace App\Providers;

use App\Events\Relations\Attached;
use App\Events\Relations\Detached;
use App\Events\UserActions\ClientActionLogRequest;
use App\Events\UserActions\ClientDocExport;
use App\Events\UserActions\ClientDocImport;
use App\Listeners\LogAttachedEvent;
use App\Listeners\LogDetachedEvent;
use App\Listeners\UserActions\LogClientLoginEvent;
use App\Listeners\UserActions\LogUserActionEvent;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Attached::class => [
            LogAttachedEvent::class
        ],
        Detached::class => [
            LogDetachedEvent::class
        ],
        Login::class => [
            LogClientLoginEvent::class
        ],
        ClientDocExport::class => [
            LogUserActionEvent::class
        ],
        ClientDocImport::class => [
            LogUserActionEvent::class
        ],
        ClientActionLogRequest::class => [
            LogUserActionEvent::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
