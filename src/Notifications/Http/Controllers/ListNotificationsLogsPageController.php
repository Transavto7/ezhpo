<?php

namespace Src\Notifications\Http\Controllers;

use App\Enums\FeaturesEnum;
use App\Http\Controllers\Controller;
use Unleash\Client\Unleash;

final class ListNotificationsLogsPageController extends Controller
{
    public function __invoke(Unleash $unleash)
    {
        if (! $unleash->isEnabled(FeaturesEnum::NOTIFICATIONS_ENABLED)) {
            return view('common.disabled-feature-page', ['title' => 'Уведомления']);
        }

        $canEmployeeRead = auth()->user()->access('employee_read');

        return view('Notifications::logs-list', [
            'canEmployeeRead' => $canEmployeeRead,
        ]);
    }
}
