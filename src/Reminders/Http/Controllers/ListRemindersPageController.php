<?php

declare(strict_types=1);

namespace Src\Reminders\Http\Controllers;

use Illuminate\Support\Facades\Auth;

final class ListRemindersPageController
{
    public function __invoke()
    {
        $user = Auth::user();

        $canCreate = $user->access('reminders_create');
        $canEdit = $user->access('reminders_edit');
        $canDelete = $user->access('reminders_delete');

        return view('Reminders::list', [
            'canCreate' => $canCreate,
            'canEdit' => $canEdit,
            'canDelete' => $canDelete,
        ]);
    }
}
