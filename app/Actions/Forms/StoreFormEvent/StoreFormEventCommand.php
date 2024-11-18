<?php

namespace App\Actions\Forms\StoreFormEvent;

use App\Enums\FormEventType;

final class StoreFormEventCommand
{
    /**
     * @var string
     */
    private $formId;
    /**
     * @var FormEventType
     */
    private $eventType;
    /**
     * @var array
     */
    private $payload;
    /**
     * @var int
     */
    private $userId;

    /**
     * @param string $formId
     * @param FormEventType $eventType
     * @param array $payload
     * @param int $userId
     */
    public function __construct(string $formId, FormEventType $eventType, array $payload, int $userId)
    {
        $this->formId = $formId;
        $this->eventType = $eventType;
        $this->payload = $payload;
        $this->userId = $userId;
    }

    public function getFormId(): string
    {
        return $this->formId;
    }

    public function getEventType(): FormEventType
    {
        return $this->eventType;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
