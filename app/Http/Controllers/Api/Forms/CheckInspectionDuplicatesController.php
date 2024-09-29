<?php

namespace App\Http\Controllers\Api\Forms;

use App\Http\Controllers\Controller;
use App\Http\Requests\InspectionDuplicatesRequest;
use App\Services\DuplicateChecker\Dto\Inspection;
use App\Services\DuplicateChecker\DuplicateInspectionChecker;
use DateTimeImmutable;

class CheckInspectionDuplicatesController extends Controller
{
    public function __invoke(InspectionDuplicatesRequest $request, DuplicateInspectionChecker $checker)
    {
        $hasDuplicates = $checker->check(
            new Inspection(
                $request->input('driverId'),
                $request->input('carId'),
                new DateTimeImmutable($request->input('date')),
                $request->input('type'),
                $request->input('formType')
            )
        );

        return response()->json(['hasDuplicates' => $hasDuplicates]);
    }
}
