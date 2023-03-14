<?php

namespace App\Dtos;

use App\{Anketa, Company, Driver};
use Spatie\DataTransferObject\DataTransferObject;

class InspectionFailedData extends DataTransferObject
{
    public ?Company $company = null;
    public ?Driver $driver = null;
    public ?Anketa $anketa = null;

    public array $phones_to_sms = [];

    public string $smsMessage = '';


}