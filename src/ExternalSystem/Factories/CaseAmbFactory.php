<?php

namespace Src\ExternalSystem\Factories;

use App\Anketa;
use Carbon\Carbon;
use Src\ExternalSystem\Dto\CaseAmbDto;
use Src\ExternalSystem\Dto\Common\DiagnosisInfoDto;
use Src\ExternalSystem\Dto\Common\DoctorDto;
use Src\ExternalSystem\Dto\Common\MedRecordDto;
use Src\ExternalSystem\Dto\Common\PersonDto;
use Src\ExternalSystem\Dto\Common\HumanNameDto;
use Src\ExternalSystem\Dto\Common\StepDto;

final class CaseAmbFactory
{
    public static function fromModel(Anketa $anketa, string $anketaIdMis, string $driverIdMis): CaseAmbDto
    {
        $openDate = Carbon::parse($anketa->created_at);

        if ($openDate->copy()->addMinutes(2)->greaterThanOrEqualTo(Carbon::now())) {
            $openDate = $openDate->subMinutes(2);
        }

        $closeDate = $openDate->copy()->addMinute();

        $comment = 'Результаты осмотра: ';
        $comment .= $anketa->tonometer . ' ' ?? '';
        $comment .= $anketa->t_people ?? '';

        $person = self::person();
        $steps = self::steps($openDate, $closeDate, $anketaIdMis);
        $medRecords = self::medRecords($closeDate, $comment, $person);

        return new CaseAmbDto(
            $openDate,
            $closeDate,
            $anketa->id,
            $anketaIdMis,
            config('external-system.id_case_aid_type'),
            config('external-system.id_payment_type'),
            config('external-system.confidentiality'),
            config('external-system.doctor_confidentiality'),
            config('external-system.curator_confidentiality'),
            config('external-system.idLPU'),
            config('external-system.id_case_result'),
            $comment,
            $driverIdMis,
            1,
            config('external-system.case_visit_type'),
            null,
            config('external-system.id_case_type'),
            null,
            null,
            $person,
            $person,
            $person,
            $steps,
            $medRecords
        );
    }

    /**
     * @return DoctorDto
     */
    private static function person(): DoctorDto
    {
        return new DoctorDto(
            new PersonDto(
                HumanNameDto::fromValues(
                    config('external-system.person.family_name'),
                    config('external-system.person.given_name'),
                    config('external-system.person.middle_name')
                ),
                config('external-system.person.id_person_mis')
            ),
            config('external-system.person.id_position'),
            config('external-system.person.id_speciality')
        );
    }

    /**
     * @param Carbon $dateStart
     * @param Carbon $dateEnd
     * @param string $anketaIdMis
     * @return StepDto[]
     */
    private static function steps(Carbon $dateStart, Carbon $dateEnd, string $anketaIdMis): array
    {
        return [
            new StepDto(
                $dateStart,
                $dateEnd,
                $anketaIdMis,
                self::person(),
                config('external-system.id_visit_place'),
                config('external-system.id_visit_purpose')
            )
        ];
    }

    private static function medRecords(Carbon $closeDate, string $comment, DoctorDto $doctorDto): array
    {
        return [
            new MedRecordDto(
                new DiagnosisInfoDto(
                    $closeDate,
                    config('external-system.id_diagnosis_type'),
                    $comment,
                    config('external-system.mkb_code')
                ),
                $doctorDto
            )
        ];
    }
}
