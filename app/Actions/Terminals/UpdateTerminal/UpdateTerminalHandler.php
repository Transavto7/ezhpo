<?php

namespace App\Actions\Terminals\UpdateTerminal;

use App\Terminal;
use Illuminate\Contracts\Bus\Dispatcher;
use Src\Users\Management\Commands\BlockUser\BlockUserCommand;
use Src\Users\Management\Commands\UnblockUser\UnblockUserCommand;
use Src\Users\Management\Commands\UpdateUser\UpdateUserCommand;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class UpdateTerminalHandler
{
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

    public function handle(UpdateTerminalCommand $command)
    {
        $terminal = Terminal::withTrashed()->find($command->getId());

        if (! $terminal) {
            throw new NotFoundHttpException('Терминал не найден');
        }

        $user = $terminal->user()->withTrashed()->first();
        if (! $user) {
            throw new NotFoundHttpException('Пользователь не найден');
        }

        $terminal->name = $command->getName();
        $terminal->blocked = $command->getBlocked();
        $terminal->pv_id = $command->getPvId();
        $terminal->stamp_id = $command->getStampId();
        $terminal->timezone = $command->getTimezone();
        $terminal->company_id = $command->getCompanyId();
        $terminal->description = $command->getDescription();

        $terminal->save();

        $this->dispatcher->dispatch(new UpdateUserCommand(
            $user,
            $user->login,
            $user->email,
            $user->password
        ));

        if ($command->getBlocked() === 1) {
            $this->dispatcher->dispatch(new BlockUserCommand($user));
        } else {
            $this->dispatcher->dispatch(new UnblockUserCommand($user));
        }

        return $terminal->id;
    }
}
