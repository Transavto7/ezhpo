<?php

declare(strict_types=1);

namespace Src\Reminders\Http\Controllers;

final class ListRemindersPageController
{
    public function __invoke()
    {
        return view('reminders::list');
    }
}
