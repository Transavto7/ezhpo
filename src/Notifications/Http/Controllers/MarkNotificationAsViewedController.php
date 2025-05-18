<?php

namespace Src\Notifications\Http\Controllers;

use Illuminate\Bus\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Src\Core\ValueObjects\Uuid;
use Src\Notifications\Commands\MarkNotificationAsViewed\MarkNotificationAsViewedCommand;

final class MarkNotificationAsViewedController
{
    public function __invoke(string $id, Dispatcher $dispatcher)
    {
        $user = Auth::user();
        $dispatcher->dispatch(new MarkNotificationAsViewedCommand(Uuid::fromString($id), $user->id));

        return response()->noContent();
    }
}
