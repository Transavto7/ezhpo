<?php

declare(strict_types=1);

namespace Src\Reminders\Http\Controllers\Selects;

use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Reminders\Enums\ReminderAction;
use Src\Reminders\Enums\ReminderSubjectType;

final class SelectSubjectTypesController
{
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(array_values(array_map(static function (string $type) {
            return [
                'id' => $type,
                'name' => trans('reminders::subject-types.'.$type),
            ];
        }, ReminderSubjectType::cases())));
    }
}
