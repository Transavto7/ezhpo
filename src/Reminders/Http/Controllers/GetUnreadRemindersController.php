<?php

declare(strict_types=1);

namespace Src\Reminders\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Src\Reminders\ConditionBuilder\ContextConditionBuilder;
use Src\Reminders\Enums\ReminderAction;
use Src\Reminders\Queries\GetRemindersByContext\GetRemindersByContextHandler;
use Src\Reminders\Queries\GetRemindersByContext\GetRemindersByContextQuery;
use Src\Reminders\Queries\GetRemindersByContext\ReminderByContextViewModel;

final class GetUnreadRemindersController
{
    public function __invoke(
        Request $request,
        ContextConditionBuilder $conditionBuilder,
        GetRemindersByContextHandler $handler
    ): JsonResponse {
        /** @var int $id */
        $id = Auth::id();
        $conditions = $conditionBuilder->build(array_merge(['subject_type' => 'car'], ['user' => $id]));

        $reminders = $handler->handle(new GetRemindersByContextQuery(ReminderAction::from('create_inspection'), $conditions));

        return response()->json(
            array_map(function (ReminderByContextViewModel $viewModel) {
                return $viewModel->toArray();
            }, $reminders)
        );
    }
}
