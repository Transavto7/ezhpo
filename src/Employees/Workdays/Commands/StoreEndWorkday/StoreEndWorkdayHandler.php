<?php
declare(strict_types=1);

namespace Src\Employees\Workdays\Commands\StoreEndWorkday;

use App\Enums\FlagPakEnum;
use App\Point;
use App\User;
use Exception;
use Src\Employees\Workdays\Eloquent\Workday;
use Src\Employees\Workdays\SmartEnum\TypeAnketaSmartEnum;
use Symfony\Component\HttpFoundation\Response;

final class StoreEndWorkdayHandler
{
    /**
     * @throws Exception
     */
    public function handle(StoreEndWorkdayCommand $command): Workday
    {
        $workDay = new Workday();

        $employee = User::where('hash_id', $command->getEmployeeId())->first();
        if (!$employee) {
            throw new Exception('Сотрудник с указанным ID не найден!', Response::HTTP_NOT_FOUND);
        }

        $point = Point::where('hash_id', $command->getPointId())->first();
        if (!$point) {
            throw new Exception('ПВ с указанным ID не найден!', Response::HTTP_NOT_FOUND);
        }

        $openWorkday = Workday::where('employee_id', $employee->id)
            ->whereDate('date', $command->getDate()->format('Y-m-d'))
            ->where('type_anketa', TypeAnketaSmartEnum::OPEN)
            ->where('admitted', 1)
            ->first();
        if (!$openWorkday) {
            throw new Exception('Сотрудник не имеет открытой смены для закрытия!', Response::HTTP_BAD_REQUEST);
        }

        $closeWorkday = Workday::where('employee_id', $employee->id)
            ->whereDate('date', $command->getDate()->format('Y-m-d'))
            ->where('type_anketa', TypeAnketaSmartEnum::CLOSE)
            ->where('admitted', 1)
            ->first();
        if ($closeWorkday) {
            throw new Exception('Сотрудник уже имеет запись в этот день', Response::HTTP_BAD_REQUEST);
        }

        $workDay->date = $command->getDate();
        $workDay->employee_id = $employee->id;
        $workDay->pv_id = $point->id;
        $workDay->type_anketa = TypeAnketaSmartEnum::CLOSE;
        $workDay->flag_pak = FlagPakEnum::INTERNAL;
        $workDay->is_real = $command->getDate()->format('d.m.Y') === date('d.m.Y');
        $workDay->admitted = true;

        $workDay->save();

        return $workDay;
    }
}
