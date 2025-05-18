<?php

declare(strict_types=1);

namespace Src\Reminders\Http\Controllers\Selects;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Reminders\Enums\ReminderStatus;

final class SelectReminderStatusesController
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(array_values(array_map(static function (string $type) {
            return [
                'id' => $type,
                'name' => ReminderStatus::from($type)->getTitle(),
            ];
        }, ReminderStatus::cases())));
    }
}
