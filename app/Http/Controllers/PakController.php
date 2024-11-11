<?php

namespace App\Http\Controllers;

use App\Enums\FormTypeEnum;
use App\FieldPrompt;
use App\Models\Forms\Form;
use App\Models\Forms\MedicForm;
use App\ValueObjects\NotAdmittedReasons;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PakController extends Controller
{
    public function index(Request $request)
    {
        if ($request->clear) {
            Form::query()->pakQueueByUser($request->user())->delete();

            return redirect(route('pak.index'));
        }

        return view('pak.index', [
            'fields' => FieldPrompt::where('type', FormTypeEnum::PAK_QUEUE)->get()
        ]);
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
            ->leftJoin('forms', 'forms.uuid', '=', 'medic_forms.forms_uuid')
            ->leftJoin('drivers', 'drivers.hash_id', '=', 'forms.driver_id')
            ->leftJoin('points', 'points.id', '=', 'forms.point_id');

        $user = $request->user();

        if ($user->access('approval_queue_view_all')) {

        } else if ($user->hasRole('head_operator_sdpo')) {
            $forms->join('points_to_users', function ($join) use ($user) {
                $join->on('forms.point_id', '=', 'points_to_users.point_id')
                    ->where('points_to_users.user_id', '=', $user->id);
            });
        } else {
            $forms->where('forms.user_id', $user->id);
        }

        $forms->where('forms.type_anketa', FormTypeEnum::PAK_QUEUE);

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
