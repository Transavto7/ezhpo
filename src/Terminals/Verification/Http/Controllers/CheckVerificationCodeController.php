<?php

declare(strict_types=1);

namespace Src\Terminals\Verification\Http\Controllers;

use Ramsey\Uuid\Uuid;
use Src\Terminals\Verification\Commands\CheckVerificationCode\CheckVerificationCodeCommand;
use Src\Terminals\Verification\Commands\CheckVerificationCode\CheckVerificationCodeHandler;
use Src\Terminals\Verification\Http\Requests\CheckVerificationCodeRequest;
use Src\Verification\Exceptions\VerificationsNotFound;

final class CheckVerificationCodeController
{
    /**
     * @throws VerificationsNotFound
     */
    public function __invoke(CheckVerificationCodeRequest $request, CheckVerificationCodeHandler $handler)
    {
        $result = $handler->handle(new CheckVerificationCodeCommand(
            Uuid::fromString($request->input('id')),
            (string) $request->input('code')
        ));

        return response()->json([
            'verify' => $result,
        ]);
    }
}
