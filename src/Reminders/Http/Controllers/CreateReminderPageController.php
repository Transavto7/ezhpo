<?php
declare(strict_types=1);

namespace Src\Reminders\Http\Controllers;

final class CreateReminderPageController
{
    public function __invoke()
    {
        return view('reminders::create');
    }
}
