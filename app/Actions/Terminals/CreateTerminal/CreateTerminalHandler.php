<?php

namespace App\Actions\Terminals\CreateTerminal;

use App\Enums\UserEntityType;
use App\Enums\UserRoleEnum;
use App\GenerateHashIdTrait;
use App\Terminal;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Hash;
use Src\Users\Management\Commands\BlockUser\BlockUserCommand;
use Src\Users\Management\Commands\CreateUser\CreateUserCommand;
use Src\Users\Management\Commands\UpdateUserAccess\UpdateUserAccessCommand;

final class CreateTerminalHandler
{
    use GenerateHashIdTrait;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @param Dispatcher $dispatcher
     */
    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function handle(CreateTerminalCommand $command)
    {
        $apiToken = Hash::make(date('H:i:s'));

        $user = $this->dispatcher->dispatch(new CreateUserCommand(
            UserEntityType::terminal(),
            time().'@ta-7.ru',
            time().'@ta-7.ru',
            $apiToken,
            $apiToken,
        ));

        $this->dispatcher->dispatch(new UpdateUserAccessCommand(
            $user,
            [UserRoleEnum::TERMINAL],
            []
        ));

        if ($command->getBlocked() === 1) {
            $this->dispatcher->dispatch(new BlockUserCommand($user));
        }

        $hashId = $this->resolveHashId();

        $terminal = Terminal::create([
            'related_user_id' => $user->id,
            'hash_id' => $hashId,
            'name' => $command->getName(),
            'timezone' => $command->getTimezone(),
            'blocked' => $command->getBlocked(),
            'pv_id' => $command->getPvId(),
            'stamp_id' => $command->getStampId(),
            'company_id' => $command->getCompanyId(),
            'description' => $command->getDescription(),
        ]);

        return $terminal->id;
    }

    private function resolveHashId(): int
    {
        $validator = function (int $hashId) {
            if (Terminal::where('hash_id', $hashId)->first()) {
                return false;
            }

            return true;
        };

        return $this->generateHashId(
            $validator,
            config('app.hash_generator.terminal.min'),
            config('app.hash_generator.terminal.max'),
            config('app.hash_generator.terminal.tries')
        );
    }
}
