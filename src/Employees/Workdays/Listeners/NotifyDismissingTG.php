<?php

namespace Src\Employees\Workdays\Listeners;

use App\Services\Notifier\TelegramNotifierService;
use Illuminate\Support\Carbon;
use Src\Employees\Workdays\Events\EmployeeDismissed;

class NotifyDismissingTG
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param EmployeeDismissed $event
     * @return void
     */
    public function handle(EmployeeDismissed $event)
    {
        $chatId = config('telegram.chats.employee-dismissed');
        if (empty($chatId)) {
            return;
        }

        $workday = $event->getWorkday();

        $employee = $workday->employee;

        $point = $workday->point;

        $message = new EmployeeMessage(
            $workday->id,
            $employee->name,
            Carbon::parse($workday->date)->toDateTimeImmutable(),
            $point->name,
        );

        (new TelegramNotifierService())->notify($chatId, strval($message));
    }
}
