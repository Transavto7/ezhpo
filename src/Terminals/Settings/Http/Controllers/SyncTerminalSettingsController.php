<?php

declare(strict_types=1);

namespace Src\Terminals\Settings\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Src\Terminals\Settings\Commands\SyncTerminalSettings\SyncTerminalSettingsCommand;
use Src\Terminals\Settings\Commands\SyncTerminalSettings\SyncTerminalSettingsHandler;
use Src\Terminals\Settings\Factories\SettingsFactory;
use Src\Terminals\Settings\ValueObjects\Settings;
use Symfony\Component\HttpFoundation\Response;

final class SyncTerminalSettingsController
{
    public function __invoke(Request $request, SyncTerminalSettingsHandler $handler)
    {
        DB::beginTransaction();
        try {
            $handler->handle(new SyncTerminalSettingsCommand(
                $request->input('terminal_ids'),
                new Settings(
                    SettingsFactory::makeMain($request->input('settings.main', [])),
                    SettingsFactory::makeSystem($request->input('settings.system', []))
                )
            ));

            DB::commit();

            return response('', Response::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
