<?php

declare(strict_types=1);

namespace Src\Notifications\Http\Controllers\Selects;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Notifications\Enums\NotificationLogAction;
use Src\Reminders\Enums\ReminderAction;

final class SelectActionsController
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(array_values(array_map(static function (string $action) {
            return [
                'id' => $action,
                'name' => NotificationLogAction::from($action)->getTitle(),
            ];
        }, NotificationLogAction::cases())));
    }
}
