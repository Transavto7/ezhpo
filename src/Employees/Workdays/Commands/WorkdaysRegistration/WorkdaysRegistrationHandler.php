<?php
declare(strict_types=1);

namespace Src\Employees\Workdays\Commands\WorkdaysRegistration;

use App\Enums\FlagPakEnum;
use App\Settings;
use App\User;
use App\ValueObjects\ForeignDevice\PressureLimit;
use App\ValueObjects\ForeignDevice\PulseLimit;
use Exception;
use Src\Employees\Workdays\Eloquent\Workday;
use Src\Employees\Workdays\SmartEnum\WorkdayEventTypeEnum;
use Src\Employees\Workdays\WorkflowOperations\EmployerWorkdayAdmitting;
use Symfony\Component\HttpFoundation\Response;

final class WorkdaysRegistrationHandler
{
    /**
     * @throws Exception
     */
    public function handle(WorkdaysRegistrationCommand $command): Workday
    {
        $workDay = new Workday();

        if (!$command->getDate() ||
            !$command->getEmployeeId() ||
            !$command->getTerminal() ||
            !$command->getTypeAnketa()->isDefined()
        ) {
            throw new Exception('Отсутствуют обязательные параметры', Response::HTTP_BAD_REQUEST);
        }

        $employee = User::where('hash_id', $command->getEmployeeId())->first();
        if (!$employee) {
            throw new \Exception('Сотрудник с указанным ID не найден!', Response::HTTP_NOT_FOUND);
        }

        // Проверка на дубликат в этот же день
        $existingWorkday = Workday::where('employee_id', $employee->id)
            ->whereDate('date', $command->getDate()->format('Y-m-d'))
            ->where('type_anketa', $command->getTypeAnketa()->getValue())
            ->where('admitted', 1)
            ->first();
        if ($existingWorkday) {
            throw new Exception('Сотрудник уже имеет запись в этот день', Response::HTTP_BAD_REQUEST);
        }

        $workDay->date = $command->getDate();
        $workDay->timezone = $command->getTerminal()->timezone ?: 3;

        // Нельзя закрыть неоткрытую смену
        if ($command->getTypeAnketa()->isClose()) {
            /** @var Workday $openWorkday */
            $openWorkday = Workday::where('employee_id', $employee->id)
                ->whereDate('date', $command->getDate()->format('Y-m-d'))
                ->where('type_anketa', WorkdayEventTypeEnum::OPEN)
                ->where('admitted', 1)
                ->first();
            if (!$openWorkday) {
                throw new Exception('Сотрудник не имеет открытой смены для закрытия!', Response::HTTP_BAD_REQUEST);
            }

            $workDay->open_workday_id = $openWorkday->id;

            // При закрытии, должна быть та же временная зона, что и при открытии!
            $diffTimezone = $workDay->timezone - $openWorkday->timezone;
            if ($diffTimezone !== 0) {
                // Приводим время в тот же часовой пояс, что и у смены открытия
                $workDay->date->setTimezone($diffTimezone > 0 ? "+$diffTimezone" : "$diffTimezone");
            }
        }

        $workDay->employee_id = $employee->id;
        $workDay->terminal_id = $command->getTerminal()->id;

        if ($command->getTerminal()->pv) {
            $workDay->point_id = $command->getTerminal()->pv->id;
        }

        if ($termometer = $command->getPeopleThermometer()) {
            $workDay->t_people = $termometer->getTemperature();
            $workDay->t_people_test_status = $termometer->isAdmitted();
        }

        if ($tonometer = $command->getTonometer()) {
            $workDay->pressure_systolic = $tonometer->getSystolic();
            $workDay->pressure_diastolic = $tonometer->getDiastolic();
            $workDay->pressure_test_status = $tonometer->isAdmitted(
                new PressureLimit(
                    Settings::DEFAULT_PRESSURE_SYSTOLIC,
                    Settings::DEFAULT_PRESSURE_DIASTOLIC
                )
            );
        }

        if ($pulse = $command->getPulse()) {
            $workDay->pulse = $pulse->getPulse();
            $workDay->pulse_test_status = $pulse->isAdmitted(
                new PulseLimit(
                    Settings::DEFAULT_PULSE_LOWER,
                    Settings::DEFAULT_PULSE_UPPER
                )
            );
        }

        if ($alcometer = $command->getAlcometer()) {
            $workDay->alcometer_result = $alcometer->getValue();
            $workDay->alcometer_mode = $alcometer->getMode();
            $workDay->alcometer_test_status = $alcometer->isAdmitted();
        } elseif (!is_null($probaAlco = $command->getProbaAlko())) {
            $workDay->alcometer_test_status = $probaAlco;
        }

        $workDay->narko_test_status = $command->getTestNarko();

        $workDay->type_anketa = $command->getTypeAnketa()->getValue();

        $workDay->photo = $command->getPhoto();
        $workDay->video = $command->getVideo();
        $workDay->flag_pak = FlagPakEnum::SDPO_A;

        $nowDatetimeWithTerminalTimezone = new \DateTime('now', new \DateTimeZone($workDay->timezone > 0 ? "+{$workDay->timezone}" : "{$workDay->timezone}"));

        $workDay->is_real = $command->getDate()->format('d.m.Y') === $nowDatetimeWithTerminalTimezone->format('d.m.Y');

        $workDay->admitted = EmployerWorkdayAdmitting::fromWorkday($workDay)->isAllowedWork();

        $workDay->save();

        return $workDay;
    }
}
