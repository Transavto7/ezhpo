<?php

declare(strict_types=1);

namespace Src\Terminals\Queries\GetSyncPageQuery;

use App\Employee;
use App\Enums\UserRoleEnum;
use App\Terminal;
use Carbon\Carbon;
use Src\Terminals\Eloquent\TerminalSettings;
use Src\Terminals\Factories\SettingsFactory;
use Src\Terminals\ValueObjects\Settings;

final class GetSyncPageHandler
{
    public function handle(GetSyncPageQuery $query): GetSyncPageResponse
    {
        $terminals = [];
        $firstTerminalId = null;
        if ($query->getTerminalIds() !== null) {
            $terminals = Terminal::query()
                ->select([
                    'terminals.id',
                    'terminals.hash_id',
                    'terminal_checks.serial_number as serial_number',
                    'terminals.name',
                    'terminals.description',
                ])
                ->leftJoin('terminal_checks', 'terminals.id', '=', 'terminal_checks.terminal_id')
                ->whereIn('terminals.id', $query->getTerminalIds())
                ->get()
                ->map(function (Terminal $model) {
                    return new TerminalViewModel(
                        $model->id,
                        sprintf(
                            '[%s] %s %s',
                            $model->hash_id,
                            $model->name,
                            $model->serial_number ? 's/n: '.$model->serial_number : ''
                        ),
                        $model->description
                    );
                })
                ->toArray();
            $firstTerminalId = $query->getTerminalIds()[0];
        }

        $medics = Employee::query()
            ->with([
                'user',
                'user.roles',
                'point:id,name,pv_id',
                'point.town:id,name',
            ])
            ->whereHas('user.roles', function ($q) {
                $q->where('roles.id', UserRoleEnum::MEDIC);
            })
            ->orderBy('name')
            ->get()
            ->map(function (Employee $medic) {
                return new MedicTerminalViewModel(
                    $medic->id,
                    $medic->name,
                    $medic->eds,
                    Carbon::parse($medic->validity_eds_start),
                    $medic->validity_eds_end ? Carbon::parse($medic->validity_eds_end) : null,
                    $medic->point->name
                );
            })
            ->toArray();

        $settings = optional(TerminalSettings::settingsForTerminal($firstTerminalId)->first())->settings;

        if ($settings === null) {
            $settings = new Settings(
                SettingsFactory::makeMain(),
                SettingsFactory::makeSystem(),
            );
        }

        return new GetSyncPageResponse(
            $terminals,
            $settings,
            $medics,
        );
    }
}
