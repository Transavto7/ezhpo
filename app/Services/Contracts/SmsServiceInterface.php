<?php

namespace App\Services\Contracts;

interface SmsServiceInterface extends ServiceInterface
{
    public function send(): bool;
}