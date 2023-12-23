<?php

namespace Src\ExternalSystem\Facades;

use Illuminate\Support\Facades\Facade;
use Src\ExternalSystem\Services\Terminology\TerminologyService;

/**
 * @mixin TerminologyService
 */
final class Terminology extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'terminology';
    }
}
