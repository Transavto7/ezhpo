<?php

namespace App\Services\OneC;

interface OneCIntegrationServiceInterface
{
    public static function integrationEnabled(): bool;

    public function healthCheck(): bool;
}
