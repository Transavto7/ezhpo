<?php
declare(strict_types=1);

namespace Src\Employees\Commands\GetAllEmployees;

use App\Settings;
use Illuminate\Support\Facades\DB;
use Ramsey\Uuid\Uuid;

final class GetAllEmployeesHandler
{
    public function handle(): array
    {
        $employeeList = DB::table('users')
            ->select([
                'users.hash_id',
                'users.name'
            ])
            ->join('model_has_roles', 'model_has_roles.model_id', '=','users.id')
            ->whereIn('model_has_roles.role_id', [1, 2])
            ->whereNull('users.deleted_at')
            ->get()
            ->toArray();

        $settingDefaultValues = [
            'pressure_systolic' => Settings::DEFAULT_PRESSURE_SYSTOLIC,
            'pressure_diastolic' => Settings::DEFAULT_PRESSURE_DIASTOLIC,
            'pulse_lower' => Settings::DEFAULT_PULSE_LOWER,
            'pulse_upper' => Settings::DEFAULT_PULSE_UPPER
        ];

        $settings = DB::table('settings')
            ->select([
                'settings.key',
                'settings.value'
            ])
            ->whereIn('settings.key', array_keys($settingDefaultValues))
            ->get()
            ->toArray();
        $settings = array_combine(array_column($settings, 'key'), array_column($settings, 'value'));

        foreach ($settingDefaultValues as $key => $defaultValue) {
            if (empty($settings[$key])) {
                $settings[$key] = $defaultValue;
            } else {
                $settings[$key] = (int)$settings[$key];
            }
        }


        return [
            'employeeList' => $employeeList,
            'settings' => $settings
        ];

//        $settingsJson = json_encode($command->getTerminalSettings()->toArray(), JSON_THROW_ON_ERROR);
//
//        if (count($command->getTerminalIds()) === 0 ) {
//            TerminalSettings::query()->where('id', '=', Uuid::NIL)->update([
//                'settings' => $settingsJson,
//            ]);
//            return;
//        }
//
//        foreach ($command->getTerminalIds() as $terminalId) {
//            TerminalSettings::updateOrInsert(
//                ['terminal_id' => $terminalId],
//                [
//                    'id' => Uuid::uuid4()->toString(),
//                    'settings' => $settingsJson,
//                    'updated_at' => now(),
//                    'created_at' => now(),
//                ]
//            );
//        }

    }
}
