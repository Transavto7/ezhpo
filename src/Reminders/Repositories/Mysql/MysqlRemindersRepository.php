<?php

declare(strict_types=1);

namespace Src\Reminders\Repositories\Mysql;

use App\Employee;
use Illuminate\Support\Facades\DB;
use Src\Core\ValueObjects\ClassifierViewModel;
use Src\Core\ValueObjects\Uuid;
use Src\Reminders\ConditionBuilder\AvailableConditions;
use Src\Reminders\Conditions\Condition;
use Src\Reminders\Entities\Reminder;
use Src\Reminders\Enums\ReminderAction;
use Src\Reminders\Enums\ReminderStatus;
use Src\Reminders\Enums\ReminderType;
use Src\Reminders\Normalizers\ReminderDatabaseNormalizer;
use Src\Reminders\Queries\GetReminderById\GetReminderRepositoryInterface;
use Src\Reminders\Queries\GetReminderById\ReminderViewModel;
use Src\Reminders\Repositories\RemindersRepository;

final class MysqlRemindersRepository implements RemindersRepository, GetReminderRepositoryInterface
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

        $type = ReminderType::from($rawReminder->type);
        $status = ReminderStatus::from($rawReminder->status);
        $action = ReminderAction::from($rawReminder->action);

        $expiresAt = $rawReminder->expires_at;
        if ($expiresAt) {
            $expiresAt = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $expiresAt);
        }

        $usersToNotify = [];
        if ($rawReminder->users_to_notify) {
            $usersToNotify = json_decode($rawReminder->users_to_notify, true);
            $usersToNotify = array_reduce($usersToNotify, function (array $carry, string $id) {
                $employee = Employee::withTrashed()->find($id);

                if (! $employee) {
                    return $carry;
                }

                return array_merge($carry, [
                    new ClassifierViewModel(
                        $id,
                        "[$employee->hash_id] $employee->name"
                    )
                ]);
            }, []);
        }

        return new ReminderViewModel(
            Uuid::fromString($rawReminder->id),
            $rawReminder->title,
            $rawReminder->content,
            $conditionsViewModels,
            $expiresAt,
            $rawReminder->expires_in_minutes,
            new ClassifierViewModel(
                $status->value(),
                $status->getTitle()
            ),
            new ClassifierViewModel(
                $action->value(),
                $action->getTitle()
            ),
            new ClassifierViewModel(
                $type->value(),
                $type->getTitle()
            ),
            $rawReminder->hidden_from_initiator !== 0,
            $usersToNotify
        );
    }
}
