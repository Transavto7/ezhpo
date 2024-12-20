<?php

namespace App\Actions\Forms\StoreFormEvent;

final class StoreFormEventCommand
{
    /**
     * @var string
     */
    private $formId;

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
     * @param array $payload
     * @param int $userId
     */
    public function __construct(string $formId, array $payload, int $userId)
    {
        $this->formId = $formId;
        $this->payload = $payload;
        $this->userId = $userId;
    }

    public function getFormId(): string
    {
        return $this->formId;
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
