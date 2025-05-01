<?php

declare(strict_types=1);

namespace Src\Terminals\Verification\Http\Controllers;

use App\Driver;
use Src\Terminals\Verification\Commands\CreateVerification\CreateVerificationCommand;
use Src\Terminals\Verification\Commands\CreateVerification\CreateVerificationHandler;
use Src\Terminals\Verification\Http\Requests\CreateVerificationRequest;
use Src\Verification\Exceptions\VerificationsNotFound;

final class CreateVerificationController
{
    /**
     * @throws \Exception
     */
    public function __invoke(CreateVerificationRequest $request, CreateVerificationHandler $handler)
    {
        /** @var string $driver_id */
        $driver_id = $request->input('driver_id');
        $driver = Driver::findOrFail($driver_id);
        try {
            $result = $handler->handle(new CreateVerificationCommand(
                $driver
            ));
        } catch (VerificationsNotFound $e) {
            return response('', 404);
        }

        return response()->json($result->toArray());
    }
}
