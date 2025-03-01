<?php

namespace App\ValueObjects\NotifyTelegramMessages;

interface MessageInterface
{
    public function __toString(): string;
}
