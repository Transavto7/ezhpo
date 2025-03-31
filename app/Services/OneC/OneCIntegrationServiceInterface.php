<?php

namespace App\Services\OneC;

interface OneCIntegrationServiceInterface
{
    public static function configFilled(): bool;

    public function healthCheck(): bool;
}
