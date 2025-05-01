<?php

declare(strict_types=1);

namespace Src\Terminals\Verification\Http\Controllers;

use Ramsey\Uuid\Uuid;
use Src\Terminals\Verification\Commands\RetrySendCode\RetrySendCodeCommand;
use Src\Terminals\Verification\Commands\RetrySendCode\RetrySendCodeHandler;
use Src\Terminals\Verification\Http\Requests\RetrySendCodeRequest;
use Src\Verification\Exceptions\VerificationSendFailed;
use Src\Verification\Exceptions\VerificationsNotFound;

final class RetrySendCodeController
{
    public function __invoke(RetrySendCodeRequest $request, RetrySendCodeHandler $handler)
    {
        try {
            $handler->handle(new RetrySendCodeCommand(Uuid::fromString($request->input('id'))));
        } catch (VerificationSendFailed $e) {
            return response('', 400);
        } catch (VerificationsNotFound $e) {
            return response('', 404);
        }

        return response()->noContent();
    }
}
