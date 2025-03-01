<?php

namespace App\Services\OneC;

interface OneCIntegrationServiceInterface
{
    public function healthCheck(): bool;
}
