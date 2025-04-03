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

final class GetRemindersByContextController
{
    public function __invoke(
        Request $request,
        ContextConditionBuilder $conditionBuilder,
        GetRemindersByContextHandler $handler
    ): JsonResponse {
        /** @var int $id */
        $id = Auth::id();
        $conditions = $conditionBuilder->build(array_merge($request->input('context', []), ['user' => $id]));
        $action = ReminderAction::from($request->input('action'));

        $reminders = $handler->handle(new GetRemindersByContextQuery($action, $conditions));

        return response()->json(
            array_map(function (ReminderByContextViewModel $viewModel) {
                return $viewModel->toArray();
            }, $reminders)
        );
    }
}
