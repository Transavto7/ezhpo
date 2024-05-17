<?php
declare(strict_types=1);

namespace App\Actions\Drivers\Import\Reader;

use App\Actions\Drivers\Import\ImportObjects\ImportedDriver;
use App\Services\Import\DateParser;
use App\Services\Import\StringSanitizer;
use App\ValueObjects\Gender;
use Carbon\Carbon;

final class Hydrator
{
    /** @var array */
    protected $attributesMap = [
        'companyInn' => 1,
        'fullName' => 2,
        'birthday' => 3,
        'companyName' => 4,
        'gender' => 5,
        'phone' => 6,
        'snils' => 7,
        'license' => 8,
        'licenseIssuedAt' => 9,
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

            if ($attribute === 'birthday') {
                $value = DateParser::parse($value);
            }

            if ($attribute === 'licenseIssuedAt') {
                $value = DateParser::parse($value);
            }

            $result[$attribute] = $value;

            return $result;
        }, []);
    }

    public function hydrate(array $row): ImportedDriver
    {
        return new ImportedDriver(
            (int) $row['companyInn'],
            $row['fullName'],
            Carbon::parse($row['birthday']),
            $row['companyName'],
            Gender::parse($row['gender']),
            $row['phone'],
            $row['snils'],
            $row['license'],
            Carbon::parse($row['licenseIssuedAt'])
        );
    }
}
