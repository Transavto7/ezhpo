<?php

declare(strict_types=1);

namespace Src\Employees\Workdays\Commands\StoreWorkdayManually;

use App\Employee;
use App\Enums\FlagPakEnum;
use App\Point;
use Exception;
use Src\Employees\Workdays\Eloquent\Workday;
use Src\Employees\Workdays\SmartEnum\WorkdayEventTypeEnum;
use Symfony\Component\HttpFoundation\Response;

final class StoreWorkdayHandler
{
    /**
     * @throws Exception
     */
    public function handle(StoreWorkdayCommand $command): Workday
    {
        $workDay = new Workday();
        $workDay->flag_pak = FlagPakEnum::INTERNAL;
        $workDay->admitted = true;

        $employee = Employee::where('hash_id', $command->getEmployeeId())->first();
        if (! $employee) {
            throw new Exception('Сотрудник с указанным ID не найден!', Response::HTTP_NOT_FOUND);
        }
        $workDay->employee_id = $employee->id;

        $point = Point::find($command->getPointId());
        if (! $point) {
            throw new Exception('ПВ с указанным ID не найден!', Response::HTTP_NOT_FOUND);
        }
        $workDay->point_id = $command->getPointId();

        if ($command->getType()->isClose()) {
            /** @var Workday $openWorkday */
            $openWorkday = Workday::where('employee_id', $employee->id)
                ->whereDate('date', $command->getDate()->format('Y-m-d'))
                ->where('type_anketa', WorkdayEventTypeEnum::OPEN)
                ->where('admitted', 1)
                ->first();
            if (! $openWorkday) {
                throw new Exception('Сотрудник не имеет открытой смены для закрытия!', Response::HTTP_BAD_REQUEST);
            }

            if ($openWorkday->date > $command->getDate()) {
                throw new Exception('Некорректное время, закрытие смены до ее открытия!', Response::HTTP_BAD_REQUEST);
            }

            $workDay->open_workday_id = $openWorkday->id;
        }

        $alreadyExistWorkday = Workday::where('employee_id', $employee->id)
            ->whereDate('date', $command->getDate()->format('Y-m-d'))
            ->where('type_anketa', $command->getType()->getValue())
            ->where('admitted', 1)
            ->first();
        if ($alreadyExistWorkday) {
            throw new Exception('Сотрудник уже имеет запись выбранного типа в этот день', Response::HTTP_BAD_REQUEST);
        }

        $workDay->date = $command->getDate();
        $workDay->type_anketa = $command->getType()->getValue();

        //TODO: заменить на корректную проверку, мб вынести
        $workDay->is_real = $command->getDate()->format('d.m.Y') === date('d.m.Y');

        $workDay->save();

        return $workDay;
    }
}
