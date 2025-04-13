<?php

declare(strict_types=1);

namespace Src\Reminders\Http\Controllers;

use Src\Core\ValueObjects\Uuid;
use Src\Reminders\Queries\GetReminderById\GetReminderByIdHandler;
use Src\Reminders\Queries\GetReminderById\GetReminderByIdQuery;

final class UpdateReminderPageController
{
    public function __invoke(string $reminderId, GetReminderByIdHandler $getReminderByIdHandler)
    {
        $viewModel = $getReminderByIdHandler->handle(new GetReminderByIdQuery(Uuid::fromString($reminderId)));

        return view('Reminders::update', [
            'reminder' => $viewModel,
        ]);
    }
}
