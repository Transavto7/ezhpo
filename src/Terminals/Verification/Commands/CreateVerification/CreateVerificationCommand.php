<?php

declare(strict_types=1);

namespace Src\Terminals\Verification\Commands\CreateVerification;

use App\Driver;

final class CreateVerificationCommand
{
    /** @var Driver */
    private $driver;

    /**
     * @param Driver $driver
     */
    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
    }

    public function getDriver(): Driver
    {
        return $this->driver;
    }
}
