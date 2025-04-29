<?php
declare(strict_types=1);

namespace Src\Terminals\Http\Controllers;

use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;
use Src\Terminals\Queries\GetSyncPageQuery\GetSyncPageHandler;
use Src\Terminals\Queries\GetSyncPageQuery\GetSyncPageQuery;
use Src\Verification\Verifiers\DefaultVerifier;

final class SyncTerminalSettingsPageController
{
    public function __invoke(Request $request, GetSyncPageHandler $handler, DefaultVerifier $verifier)
    {
        $res = $verifier->verify('123456', 'test');
        dd($res);

        $rawIds = $request->input('terminal_ids', '');
        $terminalIds = null;
        if ($rawIds !== null) {
            $terminalIds = explode(',', $rawIds);
            if (empty($terminalIds)) {
                abort(400);
            }
        }

        $response = $handler->handle(new GetSyncPageQuery($terminalIds));

        return view('terminals::sync-settings', [
            'response' => $response,
        ]);
    }
}
