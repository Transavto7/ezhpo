<?php

declare(strict_types=1);

namespace Src\Reminders\Http\Controllers;

use App\Enums\FeaturesEnum;
use Illuminate\Support\Facades\Auth;
use Unleash\Client\Unleash;

final class ListRemindersPageController
{
    public function __invoke(Unleash $unleash)
    {
        if (! $unleash->isEnabled(FeaturesEnum::REMINDERS_ENABLED)) {
            return view('common.disabled-feature-page', ['title' => 'Напоминания']);
        }

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
