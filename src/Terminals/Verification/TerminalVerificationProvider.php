<?php
declare(strict_types=1);

namespace Src\Terminals\Verification;

use Illuminate\Support\ServiceProvider;

final class TerminalVerificationProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/Http/routes.php');
    }
}
