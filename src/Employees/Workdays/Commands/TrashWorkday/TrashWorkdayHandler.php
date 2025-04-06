<?php

namespace Src\Employees\Workdays\Commands\TrashWorkday;

use App\User;
use Exception;
use Illuminate\Support\Carbon;
use LogicException;
use Src\Employees\Workdays\Eloquent\Workday;
use Src\Employees\Workdays\SmartEnum\WorkdayEventTypeEnum;
use Symfony\Component\HttpFoundation\Response;

final class TrashWorkdayHandler
{
    public function handle(Workday $workday, $action, User $user)
    {
        $workday->deleted_id = $user->id;

        if (! $action) {
            $workday = $this->restore($workday);
        } else {
            $workday = $this->trash($workday);
        }

        $workday->save();
    }

    private function trash(Workday $workday): Workday
    {
        $attachedOtherWorkday = Workday::query()
            ->where('open_workday_id', $workday->id)
            ->exists();

        if ($attachedOtherWorkday) {
            throw new LogicException('Осмотр не может быть удален, т.к. связан с другим осмотром');
        }

        $workday->open_workday_id = null;
        $workday->deleted_at = Carbon::now();

        return $workday;
    }

    /**
     * @throws Exception
     */
    private function restore(Workday $workday): Workday
    {
        $date = $workday->date->format('Y-m-d');

        $alreadyExistWorkday = Workday::query()->where('employee_id', $workday->employee_id)
            ->whereDate('date', $date)
            ->where('type_anketa', $workday->type_anketa)
            ->where('admitted', 1)
            ->first();
        if ($alreadyExistWorkday && $workday->admitted) {
            throw new Exception('Сотрудник уже имеет запись выбранного типа в этот день', Response::HTTP_BAD_REQUEST);
        }

        if ($workday->type_anketa === WorkdayEventTypeEnum::CLOSE) {
            /** @var Workday $openWorkday */
            $openWorkday = Workday::query()->where('employee_id', $workday->employee_id)
                ->whereDate('date', $date)
                ->where('type_anketa', WorkdayEventTypeEnum::OPEN)
                ->where('admitted', 1)
                ->first();

            if (! $openWorkday) {
                throw new Exception('Сотрудник не имеет открытой смены для закрытия!', Response::HTTP_BAD_REQUEST);
            }

            if ($openWorkday->date > $workday->date) {
                throw new Exception('Некорректное время, закрытие смены до ее открытия!', Response::HTTP_BAD_REQUEST);
            }

            if ($workday->admitted) {
                $workday->open_workday_id = $openWorkday->id;
            }
        }

        $workday->deleted_at = null;

        return $workday;
    }
}
