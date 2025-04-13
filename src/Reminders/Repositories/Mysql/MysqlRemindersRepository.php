<?php

declare(strict_types=1);

namespace Src\Reminders\Repositories\Mysql;

use Illuminate\Support\Facades\DB;
use Src\Core\ValueObjects\ClassifierViewModel;
use Src\Core\ValueObjects\Uuid;
use Src\Reminders\ConditionBuilder\AvailableConditions;
use Src\Reminders\Conditions\Condition;
use Src\Reminders\Entities\Reminder;
use Src\Reminders\Enums\ReminderAction;
use Src\Reminders\Normalizers\ReminderDatabaseNormalizer;
use Src\Reminders\Queries\GetReminderById\GetReminderRepositoryInterface;
use Src\Reminders\Queries\GetReminderById\ReminderViewModel;
use Src\Reminders\Queries\GetRemindersByContext\GetReminderByContextRepository;
use Src\Reminders\Queries\GetRemindersByContext\ReminderByContext;
use Src\Reminders\Repositories\RemindersRepository;

final class MysqlRemindersRepository implements RemindersRepository, GetReminderRepositoryInterface, GetReminderByContextRepository
{
    /** @var ReminderDatabaseNormalizer */
    private $normalizer;

    /**
     * @param ReminderDatabaseNormalizer $normalizer
     */
    public function __construct(ReminderDatabaseNormalizer $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    public function add(Reminder $reminder): void
    {
        DB::table('reminders')->insert(array_merge($this->normalizer->normalize($reminder), ['created_at' => now(), 'updated_at' => now()]));
    }

    public function findById(Uuid $reminderId): ?Reminder
    {
        $rawReminder = DB::table('reminders')->where('id', '=', $reminderId->value())->first();

        if ($rawReminder === null) {
            return null;
        }

        return $this->normalizer->denormalize((array) $rawReminder);
    }

    public function save(Reminder $reminder): void
    {
        DB::table('reminders')
            ->where('id', '=', $reminder->getId()->value())
            ->update(array_merge($this->normalizer->normalize($reminder), ['updated_at' => now()]));
    }

    public function remove(Uuid $reminderId): void
    {
        DB::table('reminders')->where('id', '=', $reminderId->value())->delete();
    }

    public function getReminderViewModelById(Uuid $reminderId): ?ReminderViewModel
    {
        /** @var Condition[] $availableConditions */
        $availableConditions = [];
        $rawReminderBuilder = DB::table('reminders')->where('reminders.id', '=', $reminderId->value());
        $selectArray = ['reminders.*'];
        foreach (AvailableConditions::AVAILABLE_CONDITIONS as $conditionClass) {
            /** @var class-string<Condition> $conditionClass */
            $condition = $conditionClass::create();
            $condition->addJoin($rawReminderBuilder);
            $selectArray = array_merge($selectArray, $condition->getSelectFields());
            $availableConditions[] = $condition;
        }

        $rawReminder = $rawReminderBuilder->select($selectArray)->first();

        if ($rawReminder === null) {
            return null;
        }

        $conditionsViewModels = [];

        foreach ($availableConditions as $condition) {
            $conditionsViewModels[$condition->getName()] = $condition->makeViewModel((array) $rawReminder);
        }

        return new ReminderViewModel(
            Uuid::fromString($rawReminder->id),
            $rawReminder->title,
            $rawReminder->content,
            new ClassifierViewModel($rawReminder->action, trans('reminders::actions.'.$rawReminder->action)),
            $conditionsViewModels,
            $rawReminder->status,
            $rawReminder->type,
        );
    }

    /**
     * @param ReminderAction $action
     * @param Condition[] $context
     * @return ReminderByContext[]
     */
    public function getReminderByContext(ReminderAction $action, array $context): array
    {
        $builder = DB::table('reminders')
            ->select([
                'reminders.id',
                'reminders.title',
                'reminders.content',
                'reminders.expires_at',
                'reminders.expires_in_minutes',
                'reminders.hidden_from_initiator',
                'reminders.users_to_notify'
            ])
            ->where('action', '=', $action->value())
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
