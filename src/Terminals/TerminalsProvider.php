<?php
declare(strict_types=1);

namespace Src\Terminals;

use Illuminate\Support\ServiceProvider;
use Src\Terminals\Settings\TerminalsSettingsProvider;
use Src\Terminals\Verification\TerminalVerificationProvider;

final class TerminalsProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->register(TerminalsSettingsProvider::class);
        $this->app->register(TerminalVerificationProvider::class);
    }
}
