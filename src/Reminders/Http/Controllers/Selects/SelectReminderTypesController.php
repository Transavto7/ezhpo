<?php

declare(strict_types=1);

namespace Src\Reminders\Http\Controllers\Selects;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Reminders\Enums\ReminderSubjectType;
use Src\Reminders\Enums\ReminderType;

final class SelectReminderTypesController
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(array_values(array_map(static function (string $type) {
            return [
                'id' => $type,
                'name' => ReminderType::from($type)->getTitle(),
            ];
        }, ReminderType::cases())));
    }
}
