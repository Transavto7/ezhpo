<?php
declare(strict_types=1);

namespace App\Actions\Terminals\SetManyTerminalsDescription;

use App\Http\Controllers\Terminals\SetManyTerminalsDescriptionController;
use App\Terminal;

final class SetManyTerminalsDescriptionHandler
{
    public function handle(SetManyTerminalsDescriptionCommand $command): void
    {
        Terminal::query()
            ->whereIn('id', $command->getTerminalIds())
            ->update(['description' => $command->getDescription()]);
    }
}
