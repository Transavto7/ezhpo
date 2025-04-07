<?php

namespace Src\Employees\Workdays\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Src\Employees\Workdays\Events\EmployeeDismissed;
use Src\Employees\Workdays\Listeners\NotifyDismissingTG;

class WorkdaysEventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        EmployeeDismissed::class => [
            NotifyDismissingTG::class,
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
