<?php

namespace App\Dtos;

use App\Http\Requests\WorkReportRequest;
use DateTime;
use Spatie\DataTransferObject\DataTransferObject;

class WorkReportData extends DataTransferObject
{
    public int $pv_id;
    public int $user_id;
}