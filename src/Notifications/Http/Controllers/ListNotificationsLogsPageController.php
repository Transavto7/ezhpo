<?php

namespace Src\Notifications\Http\Controllers;

use App\Http\Controllers\Controller;

final class ListNotificationsLogsPageController extends Controller
{
    public function __invoke()
    {
        $canEmployeeRead = auth()->user()->access('employee_read');

        return view('Notifications::logs-list', [
            'canEmployeeRead' => $canEmployeeRead,
        ]);
    }
}
