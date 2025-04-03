<?php

declare(strict_types=1);

namespace Src\Reminders\Http\Controllers\Selects;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Src\Reminders\Enums\ReminderSubjectType;
use Src\Reminders\Factories\SubjectBuilderFactory;

final class SelectSubjectsController
{
    /**
     * @throws \Exception
     */
    public function __invoke(Request $request, SubjectBuilderFactory $factory): JsonResponse
    {
        $builder = $factory->createBuilder(
            ReminderSubjectType::from($request->input('subject_type')),
        );

        $subjects = DB::table(DB::raw("({$builder->toSql()}) as main"))
            ->select('main.*')
            ->when($request->input('search'), function (Builder $builder) use ($request) {
                return $builder->where('name', 'like', "%{$request->input('search')}%");
            })
            ->get();

        return response()->json($subjects->toArray());
    }
}
