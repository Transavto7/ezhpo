<?php

namespace Src\Notifications\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Src\Notifications\Enums\NotificationFilterStatus;

final class ListNotificationsPageController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();

        $canViewOther = $user->access('notifications_view_other');
        $canChangeOther = $user->access('notifications_change_other');
        $canEmployeeRead = $user->access('employee_read');
        $canRemindersRead = $user->access('reminders_read');

        $statusOptions = array_reduce(NotificationFilterStatus::cases(), function (array $carry, string $status) {
            $status = NotificationFilterStatus::from($status);

            $carry[] = [
                'id' => $status->value(),
                'name' => $status->getTitle(),
            ];

            return $carry;
        }, []);

        return view('Notifications::list', [
            'canViewOther' => $canViewOther,
            'canChangeOther' => $canChangeOther,
            'canEmployeeRead' => $canEmployeeRead,
            'canRemindersRead' => $canRemindersRead,
            'statusOptions' => $statusOptions,
        ]);
    }
}
