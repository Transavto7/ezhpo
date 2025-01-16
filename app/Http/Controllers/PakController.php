<?php

namespace App\Http\Controllers;

use App\Actions\Anketa\ChangeSdpoMedicFormType\ChangeSdpoMedicFormTypeHandler;
use App\Enums\FormTypeEnum;
use App\FieldPrompt;
use App\Models\Forms\Form;
use App\Models\Forms\MedicForm;
use App\ValueObjects\NotAdmittedReasons;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;

class PakController extends Controller
{
    public function index()
    {
        return view('pak.index', [
            'fields' => FieldPrompt::where('type', FormTypeEnum::PAK_QUEUE)->get()
        ]);
    }

    public function clear(ChangeSdpoMedicFormTypeHandler $handler)
    {
        $user = Auth::user();

        $formsIds = Form::query()
            ->select('forms.id')
            ->pakQueueByUser($user)
            ->get()
            ->pluck('id')
            ->toArray();

        $errors = [];

        foreach ($formsIds as $formsId) {
            try {
                DB::beginTransaction();

                $handler->handle($formsId, $user);

                DB::commit();
            } catch (Throwable $exception) {
                $errors[] = "$formsId . {$exception->getMessage()}";

                DB::rollBack();
            }
        }

        return redirect(route('pak.index'))->withErrors($errors);
    }

    public function list(Request $request): JsonResponse
    {
        $forms = MedicForm::query()
            ->select([
                'forms.*',
                'medic_forms.*',
                'drivers.fio as driver_fio',
                'points.name as pv_id'
            ])
            ->join('forms', 'forms.uuid', '=', 'medic_forms.forms_uuid')
            ->pakQueueByUser($request->user())
            ->leftJoin('drivers', 'drivers.hash_id', '=', 'forms.driver_id')
            ->leftJoin('points', 'points.id', '=', 'forms.point_id')
            ->where('forms.date', '>=', Carbon::now()->subDay());

        if ($request->order_key) {
            $forms = $forms->orderBy($request->order_key, $request->order_by ?? 'ASC');
        }

        $forms->get();

        $data = $forms->get()->map(function (MedicForm $form) {
            return array_merge(
                $form->toArray(),
                ['not_admitted_reasons' => NotAdmittedReasons::fromForm($form)->getReasons()]
            );
        });

        return response()->json($data);
    }
}
