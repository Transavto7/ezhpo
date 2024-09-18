<?php

namespace App\Services;

use App\Anketa;
use Exception;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class DuplicatesCheckerService
{
    /**
     * @throws Exception
     */
    public static function checkExist(Arrayable $existForms, $formTimestamp)
    {
        foreach($existForms as $existForm) {
            if (self::isDuplicate($formTimestamp, $existForm->date)) {
                throw new Exception("Найден дубликат осмотра (ID: $existForm->id, Дата: $existForm->date)");
            }
        }
    }

    /**
     * @throws Exception
     */
    public static function checkCreating(array $forms, $formTimestamp)
    {
        $formDuplicates = 0;
        $errorMessage = null;
        foreach ($forms as $form) {
            if (self::isDuplicate($formTimestamp, $form['date'])) {
                $errorMessage = "Найден дубликат осмотра при добавлении (Дата: $form[date])";
                $formDuplicates++;
            }
        }

        if ($formDuplicates > 1) {
            throw new Exception($errorMessage);
        }
    }

    protected static function isDuplicate($first, $second): bool
    {
        $diffInMinutes = abs($first - Carbon::parse($second)->timestamp);

        return ($diffInMinutes < Anketa::MIN_DIFF_BETWEEN_FORMS_IN_SECONDS) && ($diffInMinutes >= 0);
    }

    /**
     * @param array $carsId
     * @param Carbon[] $dateDiapason
     * @return Collection
     */
    public static function getExistTechForms(array $carsId, array $dateDiapason = []): Collection
    {
        if (count($carsId) === 0) {
            return collect([]);
        }

        $query = Anketa::query()
            ->select([
                'id',
                'date'
            ]);

        if (count($carsId) === 1) {
            $query = $query->where('car_id', $carsId[0]);
        } else {
            $query = $query->where(function (Builder $subQuery) use ($carsId) {
                foreach ($carsId as $car) {
                    $subQuery->orWhere('car_id', $car);
                }
            });
        }

        if (count($dateDiapason) == 2) {
            $query->whereBetween('date', $dateDiapason);
        }

        return $query->where('type_anketa', 'tech')
            ->where('in_cart', 0)
            ->whereNotNull('date')
            ->where(function (Builder $query) {
                $query
                    ->where('is_dop', '<>', 1)
                    ->orWhereNotNull('result_dop');
            })
            ->orderBy('date', 'desc')
            ->get();
    }

    /**
     * @param $driverId
     * @param Carbon[] $dateDiapason
     * @return Collection
     */
    public static function getExistMedicForms($driverId, array $dateDiapason = []): Collection
    {
        $query = Anketa::query()
            ->select([
                'id',
                'date'
            ])
            ->where('driver_id', $driverId)
            ->where('type_anketa', 'medic')
            ->where('in_cart', 0)
            ->whereNotNull('date')
            ->where(function (Builder $query) {
                $query
                    ->where('is_dop', '<>', 1)
                    ->orWhereNotNull('result_dop');
            })
            ->orderBy('date', 'desc');

        if (count($dateDiapason) == 2) {
            $query->whereBetween('date', $dateDiapason);
        }

        return $query->get();
    }
}
