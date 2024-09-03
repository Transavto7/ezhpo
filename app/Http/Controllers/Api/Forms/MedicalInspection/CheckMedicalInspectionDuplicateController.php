<?php

namespace App\Http\Controllers\Api\Forms\MedicalInspection;

use App\Http\Controllers\Controller;
use App\Services\DuplicateChecker\Dto\MedicalInspection;
use App\Services\DuplicateChecker\DuplicateMedicalInspectionChecker;
use DateTimeImmutable;
use Illuminate\Http\Request;

class CheckMedicalInspectionDuplicateController extends Controller
{
    public function __invoke(Request $request, DuplicateMedicalInspectionChecker $checker)
    {
        $hasDuplicates = $checker->check(
            new MedicalInspection(
                $request->input('driverId'),
                new DateTimeImmutable($request->input('date')),
                $request->input('type')
            )
        );

        return response()->json(['hasDuplicates' => $hasDuplicates]);
    }
}
