<?php

declare(strict_types=1);

namespace Src\Reminders\Normalizers;

use DateTimeImmutable;
use Src\Core\ValueObjects\Uuid;
use Src\Reminders\Entities\Reminder;
use Src\Reminders\Enums\ReminderAction;
use Src\Reminders\Enums\ReminderStatus;
use Src\Reminders\Enums\ReminderType;

final class ReminderDatabaseNormalizer
{
    /** @var ReminderContextDatabaseNormalizer */
    private $contextNormalizer;

    /**
     * @param ReminderContextDatabaseNormalizer $contextNormalizer
     */
    public function __construct(ReminderContextDatabaseNormalizer $contextNormalizer)
    {
        $this->contextNormalizer = $contextNormalizer;
    }

    /**
     * @param Reminder $reminder
     * @return array<string, string>
     */
    public function normalize(Reminder $reminder): array
    {
        return [
            'id' => $reminder->getId()->value(),
            'title' => $reminder->getTitle(),
            'content' => $reminder->getContent(),
            'status' => $reminder->getStatus()->value(),
            'type' => $reminder->getType()->value(),
            'action' => $reminder->getAction()->value(),
            'context' => json_encode($this->contextNormalizer->normalize($reminder->getContext())),
            'expires_at' => $reminder->getExpiresAt() ? $reminder->getExpiresAt()->format('Y-m-d H:i') : null,
            'expires_in_minutes' => $reminder->getExpiresInMinutes(),
            'hidden_from_initiator' => $reminder->isHiddenFromInitiator(),
            'users_to_notify' => json_encode($reminder->getUsersToNotify()),
            'one_time_per_user' => $reminder->isOneTimePerUser(),
            'until_any_user_completes' => $reminder->isUntilAnyUserCompletes(),
        ];
    }

    /**
     * @param array<string, string> $reminder
     * @return Reminder
     */
    public function denormalize(array $reminder): Reminder
    {
        $context = json_decode($reminder['context'], true);

        return new Reminder(
            Uuid::fromString($reminder['id']),
            $reminder['title'],
            $reminder['content'],
            ReminderAction::from($reminder['action']),
            $this->contextNormalizer->denormalize($context),
            ReminderStatus::from($reminder['status']),
            ReminderType::from($reminder['type']),
            (bool) $reminder['hidden_from_initiator'] ?? false,
            json_decode($reminder['users_to_notify'] ?? '[]', true),
            (bool) $reminder['one_time_per_user'] ?? false,
            (bool) $reminder['until_any_user_completes'] ?? false,
            $reminder['expires_at'] ? DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $reminder['expires_at']) : null,
            $reminder['expires_in_minutes'] ?? null
        );
    }
}
