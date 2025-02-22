<?php

namespace App\Http\Controllers\Terminals;

use App\Models\Forms\MedicForm;
use App\Terminal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class GetTerminalsTableItemsController
{
    public function __invoke(Request $request)
    {
        $builder = Terminal::query()
            ->select([
                'terminals.*',
            ])
            ->with([
                'point',
                'point.town',
                'stamp',
                'company',
                'terminalCheck',
                'terminalSettings',
                'user' => function ($query) {
                    return $query->withTrashed();
                },
            ])
            ->leftJoin('terminal_settings', 'terminals.id', '=', 'terminal_settings.terminal_id');

        if ($request->get('deleted')) {
            $builder->with(['whoDeleted'])->onlyTrashed();
        }

        $pointIds = $request->get('point_id');
        if ($pointIds) {
            $builder->whereHas('point', function ($query) use ($pointIds) {
                $query->whereIn('id', $pointIds);
            });
        }

        $settingsFilter = $request->get('settings');
        if ($settingsFilter && $settingsFilter !== 'all') {
            if ($settingsFilter === 'with_settings') {
                $builder->whereNotNull('terminal_settings.id');
            }
            if ($settingsFilter === 'without_settings') {
                $builder->whereNull('terminal_settings.id');
            }
        }

        $companyIds = $request->get('company_id');
        if ($companyIds) {
            $builder->whereIn('company_id', $companyIds);
        }

        $terminalIds = $request->get('terminal_id');
        if ($terminalIds) {
            $builder->whereIn('terminals.id', $terminalIds);
        }

        $townIds = $request->get('town_id');
        if ($townIds) {
            $builder->whereHas('point.town', function ($query) use ($townIds) {
                $query->whereIn('id', $townIds);
            });
        }

        $dateCheck = $request->get('date_check');
        if ($dateCheck) {
            $builder->whereHas('terminalCheck', function ($query) use ($dateCheck) {
                $query->where('date_end_check', '>=', Carbon::parse($dateCheck)->startOfDay());
            });
        }

        $toDateCheck = $request->input('TO_date_check');
        if ($toDateCheck) {
            $builder->whereHas('terminalCheck', function ($query) use ($toDateCheck) {
                $query->where('date_end_check', '<=', Carbon::parse($toDateCheck)->endOfDay());
            });
        }

        $orderBy = $request->get('sortBy', 'id');
        $orderDirection = filter_var($request->get('sortDesc'), FILTER_VALIDATE_BOOLEAN) ? 'desc' : 'asc';
        if ($orderBy) {
            $terminalColumns = ['hash_id', 'name', 'blocked', 'stamp_id', 'company_id', 'timezone'];
            $terminalCheckColumns = ['date_end_check', 'serial_number'];

            if (in_array($orderBy, $terminalColumns)) {
                $builder->orderBy($orderBy, $orderDirection);
            }

            if (in_array($orderBy, $terminalCheckColumns)) {
                $builder
                    ->join('terminal_checks', 'terminal_checks.terminal_id', '=', 'terminals.id')
                    ->select('terminals.*', 'terminal_checks.'.$orderBy)
                    ->orderBy("terminal_checks.$orderBy", $orderDirection)
                    ->groupBy(['terminals.id']);
            }
        }

        $lastMonthAmount = MedicForm::query()
            ->select([
                DB::raw('count(forms.id) as count'),
                'medic_forms.terminal_id',
            ])
            ->leftJoin('forms', 'forms.uuid', '=', 'medic_forms.forms_uuid')
            ->where('forms.created_at', '>=', Carbon::now()->subMonth()->startOfMonth())
            ->where('forms.created_at', '<=', Carbon::now()->startOfMonth())
            ->whereNotNull('medic_forms.terminal_id')
            ->groupBy(['medic_forms.terminal_id'])
            ->get()
            ->pluck('count', 'terminal_id')
            ->toArray();

        $monthAmount = MedicForm::query()
            ->select([
                DB::raw('count(forms.id) as count'),
                'medic_forms.terminal_id',
            ])
            ->leftJoin('forms', 'forms.uuid', '=', 'medic_forms.forms_uuid')
            ->where('forms.created_at', '>', Carbon::now()->startOfMonth())
            ->whereNotNull('medic_forms.terminal_id')
            ->groupBy(['medic_forms.terminal_id'])
            ->get()
            ->pluck('count', 'terminal_id')
            ->toArray();

        $paginator = $builder->paginate(100);

        $terminals = $paginator
            ->getCollection()
            ->map(function (Terminal $terminal) use ($lastMonthAmount, $monthAmount) {
                $whoDeleted = null;
                if ($terminal->whoDeleted) {
                    $whoDeleted = $terminal->whoDeleted->name;
                }

                $deletedAt = null;
                if ($terminal->deleted_at) {
                    $deletedAt = $terminal->deleted_at->format('Y-m-d H:i:s');
                }

                $townName = null;
                if ($terminal->point && $terminal->point->town) {
                    $townName = $terminal->point->town->name;
                }

                $companyName = null;
                if ($terminal->company) {
                    $companyName = $terminal->company->name;
                }

                $dateEndCheck = null;
                if ($terminal->terminalCheck) {
                    $dateEndCheck = $terminal->terminalCheck->date_end_check;
                }

                return [
                    'id' => $terminal->id,
                    'hash_id' => $terminal->hash_id,
                    'related_user_id' => $terminal->related_user_id,
                    'name' => $terminal->name,
                    'blocked' => $terminal->blocked,
                    'api_token' => $terminal->user->api_token,
                    'timezone' => $terminal->timezone,
                    'serial_number' => $terminal->terminalCheck ? $terminal->terminalCheck->serial_number : null,
                    'pv' => $terminal->point ? $terminal->point->name : null,
                    'town' => $townName,
                    'company_id' => $companyName,
                    'stamp_id' => $terminal->stamp ? $terminal->stamp->name : null,
                    'deleted' => $deletedAt,
                    'who_deleted' => $whoDeleted,
                    'deleted_at' => $deletedAt,
                    'date_end_check' => $dateEndCheck,
                    'month_amount' => $monthAmount[$terminal->id] ?? 0,
                    'last_month_amount' => $lastMonthAmount[$terminal->id] ?? 0,
                ];
            });

        return response([
            'total_rows' => $paginator->total(),
            'current_page' => $paginator->currentPage(),
            'items' => $terminals,
        ]);
    }
}
