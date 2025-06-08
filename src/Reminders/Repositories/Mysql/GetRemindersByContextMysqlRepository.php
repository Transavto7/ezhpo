<?php

namespace Src\Reminders\Repositories\Mysql;

use Illuminate\Support\Facades\DB;
use Src\Core\ValueObjects\Uuid;
use Src\Reminders\Conditions\Condition;
use Src\Reminders\Enums\ReminderAction;
use Src\Reminders\Enums\ReminderStatus;
use Src\Reminders\Repositories\GetRemindersByContextRepository;
use Src\Reminders\ValueObjects\ReminderByContext;

final class GetRemindersByContextMysqlRepository implements GetRemindersByContextRepository
{
    /**
     * @param ReminderAction $action
     * @param Condition[] $context
     * @return ReminderByContext[]
     */
    public function getRemindersByContext(ReminderAction $action, array $context): array
    {
        $builder = DB::table('reminders')
            ->select([
                'reminders.id',
                'reminders.title',
                'reminders.content',
                'reminders.expires_at',
                'reminders.expires_in_minutes',
                'reminders.hidden_from_initiator',
                'reminders.users_to_notify',
            ])
            ->where('reminders.action', '=', $action->value())
            ->where('reminders.status', '=', ReminderStatus::ENABLE)
            ->whereNull('reminders.deleted_at')
            ->orderBy('reminders.created_at', 'desc');

        foreach ($context as $condition) {
            $builder = $condition->run($builder);
        }

        /** @var object{id: string, title: string, content: string}[] $rawReminders */
        $rawReminders = $builder->get()->toArray();

        return array_map(function (object $reminder) {
            return new ReminderByContext(
                Uuid::fromString($reminder->id),
                $reminder->title,
                $reminder->content,
                ((bool) $reminder->hidden_from_initiator) ?? false,
                json_decode($reminder->users_to_notify ?? '[]', true),
                $reminder->expires_at ? \DateTimeImmutable::createFromFormat('Y-m-d H:i', $reminder->expires_at) : null,
                $reminder->expires_in_minutes,
            );
        }, $rawReminders);
    }
}
