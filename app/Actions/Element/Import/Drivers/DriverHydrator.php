<?php
declare(strict_types=1);

namespace App\Actions\Element\Import\Drivers;

use App\Actions\Element\Import\Core\ElementHydrator;
use App\Actions\Element\Import\Drivers\ImportObjects\ImportedDriver;
use App\Services\Import\DateParser;
use App\Services\Import\StringSanitizer;
use App\ValueObjects\Gender;
use Carbon\Carbon;

final class DriverHydrator extends ElementHydrator
{
    /** @var array */
    protected $attributesMap = [
        'companyInn' => 0,
        'fullName' => 1,
        'birthday' => 2,
        'companyName' => 3,
        'gender' => 4,
        'phone' => 5,
        'snils' => 6,
        'license' => 7,
        'licenseIssuedAt' => 8,
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

            if ($attribute === 'phone' && is_numeric($value)) {
                $value = (string)$value;
            }

            if ($attribute === 'license') {
                $value = StringSanitizer::sanitizeDriverLicense($value);
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
            $row['gender'] !== null ? Gender::parse($row['gender']) : null,
            $row['phone'],
            $row['snils'],
            $row['license'],
            $row['licenseIssuedAt'] ? Carbon::parse($row['licenseIssuedAt']) : null
        );
    }
}
