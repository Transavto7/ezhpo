<?php

namespace App\ValueObjects\ForeignDevice\NotifyTelegramMessages;

interface MessageInterface
{
    public function __toString(): string;
}
