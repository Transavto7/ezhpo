<?php
declare(strict_types=1);

namespace App\Actions\Element\Import\Cars;

use App\Actions\Element\Import\Cars\ImportObjects\ImportedCar;
use App\Actions\Element\Import\Core\ElementHydrator;
use App\Actions\Element\Import\Drivers\ImportObjects\ImportedDriver;
use App\Services\Import\DateParser;
use App\Services\Import\StringSanitizer;
use App\ValueObjects\Gender;
use Carbon\Carbon;

final class CarHydrator extends ElementHydrator
{
    /** @var array */
    protected $attributesMap = [
        'companyName' => 0,
        'companyInn' => 1,
        'number' => 2,
        'markAndModel' => 3,
        'category' => 4,
        'trailer' => 5,
        'dateTechView' => 6,
        'dateOsago' => 7,
        'dateSkzi' => 8,
    ];

    private $dates = [
        'dateTechView',
        'dateOsago',
        'dateSkzi',
    ];

    /**
     * @param array $record
     * @return array
     * @throws \Exception
     */
    public function associate(array $record): array
    {
        return array_reduce(array_keys($this->attributesMap), function ($result, $attribute) use ($record) {
            $value = StringSanitizer::sanitize($record[$this->attributesMap[$attribute]]);

            if (in_array($attribute, $this->dates)) {
                $value = DateParser::parse($value);
            }

            $result[$attribute] = $value;

            return $result;
        }, []);
    }

    public function hydrate(array $row): ImportedCar
    {
        return new ImportedCar(
            $row['companyName'],
            (string)$row['companyInn'],
            $row['number'],
            $row['markAndModel'],
            $row['category'],
            $row['trailer'],
            $row['dateTechView'] ? Carbon::parse($row['dateTechView']) : null,
            $row['dateOsago'] ? Carbon::parse($row['dateOsago']) : null,
            $row['dateSkzi'] ? Carbon::parse($row['dateSkzi']) : null
        );
    }
}
