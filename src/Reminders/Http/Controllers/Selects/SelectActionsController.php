<?php

declare(strict_types=1);

namespace Src\Reminders\Http\Controllers\Selects;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Reminders\Enums\ReminderAction;

final class SelectActionsController
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(array_values(array_map(static function (string $action) {
            return [
                'id' => $action,
                'name' => trans('reminders::actions.'.$action),
            ];
        }, ReminderAction::cases())));
    }
}
