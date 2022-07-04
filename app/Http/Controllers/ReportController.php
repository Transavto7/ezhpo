<?php

namespace App\Http\Controllers;

use App\Anketa;
use App\Discount;
use App\Product;
use App\Req;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public $reports = [
        'journal' => 'Отчет по услугам компании',
        'graph_pv' => 'График работы пунктов выпуска'
    ];

    public function GetReport(Request $request)
    {
        if(auth()->user()->hasRole('medic', '==') || auth()->user()->hasRole('tech', '==')) {
            return back();
        }

        $data = $request->all();
        $isApi = isset($_GET['api']);
        $type_report = $request->type_report;
        $indexC = new IndexController();

        $company_fields = $indexC->elements['Driver']['fields']['company_id'];
        $company_fields['getFieldKey'] = 'hash_id';

        $pv_fields = $indexC->elements['Company']['fields']['pv_id'];
        $pv_fields['getFieldKey'] = 'name';
        $pv_fields['multiple'] = 1;

        $date_field = 'date';
        $date_from = isset($data['date_from']) ? $data['date_from'] : '';
        $date_to = isset($data['date_to']) ? $data['date_to'] : date('Y-m-d');
        $date_from_time = $request->get('date_from_time', '00:00:00');
        $date_to_time = $request->get('date_from_time', '23:59:59');

        $pv_id = isset($data['pv_id']) ? $data['pv_id'] : [0];

        $dopData = [];

        $reports = null;
        $reports2 = null;

        if(isset($data['filter'])) {
            $period_def = CarbonPeriod::create($date_from, $date_to)->month();
            $months_def = collect($period_def)->map(function (Carbon $date) {
                return $date->month;
            })->toArray();

            switch($type_report) {
                /**
                 * ГРАФИК РАБОТЫ ПВ
                 */
                case 'graph_pv':

                    if($isApi) {

                        $reports = Anketa::whereIn('pv_id', $pv_id)
                            ->where('type_anketa', 'medic')
                            ->where('in_cart', 0)
                            ->whereRaw("(date >= ? AND date <= ?)", [
                                $date_from . " " . '00:00:00',
                                $date_to . " " . '23:59:59'
                            ]);

                        $reports2 = Anketa::whereIn('pv_id', $pv_id)
                            ->where('type_anketa', 'medic')
                            ->where('in_cart', 0)
                            ->whereRaw("(created_at >= ? AND created_at <= ?)", [
                                $date_from." ".'00:00:00',
                                $date_to." ".'23:59:59'
                            ]);

                        if($date_from_time && $date_to_time) {
                            $reports->whereTime('date', '>=', $date_from_time)
                                ->whereTime('date', '<=', $date_to_time);

                            $reports2->whereTime('created_at', '>=', $date_from_time)
                                ->whereTime('created_at', '<=', $date_to_time);
                        }

                        $reports = $reports->get();
                        $reports2 = $reports2->get();

                        return [
                            'reports' => $reports,
                            'reports2' => $reports2
                        ];
                    }

                    break;
            }
        }

        return view('pages.reports.all', [
            'title' => $this->reports[$type_report],
            'reports' => $reports,
            'reports2' => $reports2,
            'company_fields' => $company_fields, 'pv_fields' => $pv_fields,
            'type_report' => $type_report,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'date_field' => $date_field,
            'company_id' => isset($data['company_id']) ? $data['company_id'] : 0,
            'pv_id' => isset($data['pv_id']) ? $data['pv_id'] : 0,
            'data' => $dopData
        ]);
    }

    public function showJournal() {
        return view('reports.journal');
    }

    public function getJournalData(Request $request) {
        $company = $request->company_id;
        $date_to = $request->date_to;
        $date_from = $request->date_from;

        if (!$company || !$date_to || !$date_from) {
            return response(null, 404);
        }

        $products = Product::all();
        $discounts = Discount::all();
        return [
            'medics' => $this->getJournalMedic($company, $date_from, $date_to, $products, $discounts),
            'techs' => $this->getJournalTechs($company, $date_from, $date_to, $products, $discounts),
            'medics_other' => $this->getJournalMedicsOther($company, $date_from, $date_to),
            'techs_other' => $this->getJournalTechsOther($company, $date_from, $date_to),
            'other_pl' => $this->getJournalPl($company, $date_from, $date_to),
        ];
    }

    public function getJournalMedic($company, $date_from, $date_to, $products, $discounts) {
        // Get table info by filters
        $medics = Anketa::whereIn('type_anketa', ['medic', 'bdd', 'report_cart'])
            ->join('drivers', 'anketas.driver_id', '=', 'drivers.hash_id')
            ->where('anketas.company_id', $company)
            ->where('anketas.in_cart', 0)
            ->where('is_dop', 0)
            ->whereBetween('anketas.date', [
                $date_from." 00:00:00",
                $date_to." 23:59:59"
            ])
            ->select('driver_fio', 'driver_id', 'type_anketa', 'type_view', 'drivers.products_id', DB::raw('count(*) as total'))
            ->groupBy(['type_view', 'driver_id'])
            ->get();

        $medicTypeCounts = Anketa::whereIn('type_anketa', ['medic', 'bdd', 'report_cart'])
            ->whereIn('driver_id', $medics->pluck('driver_id')->toArray())
            ->join('drivers', 'anketas.driver_id', '=', 'drivers.hash_id')
            ->where('anketas.company_id', $company)
            ->where('anketas.in_cart', 0)
            ->where('is_dop', 0)
            ->whereBetween('anketas.date', [
                $date_from." 00:00:00",
                $date_to." 23:59:59"
            ])
            ->select('driver_id', 'type_anketa', 'drivers.products_id', DB::raw('count(*) as total'))
            ->groupBy(['type_anketa', 'driver_id'])
            ->get();

        $result = [];
        foreach ($medicTypeCounts as $row) {
            $result[$row->driver_id]['types'][$row->type_anketa]['total'] = $row->total;
        }

        foreach ($medics as $row) {
            $result[$row->driver_id]['driver_fio'] = $row->driver_fio;
            $result[$row->driver_id]['types'][$row->type_view]['total'] = $row->total;

            $services = explode(',', $row->products_id);
            $prods = $products->whereIn('id', $services);

            if ($prods->count() > 0) {
                foreach ($prods as $service) {
                    $discounts = $discounts->where('products_id', $service->id);

                    if($discounts->count()) {
                        foreach($discounts as $discount) {
                            $service->price_unit = $discount->add($row->total, $service->price_unit);
                        }
                    }
                }

                $result[$row->driver_id]['types'][$row->type_view]['sum'] = $prods->sum('price_unit');
            }

        }

        return $result;
    }

    public function getJournalTechs($company, $date_from, $date_to, $products, $discounts) {
        // Get table info by filters
        $techs = Anketa::whereIn('type_anketa', ['tech', 'bdd', 'report_cart'])
            ->join('cars', 'anketas.car_id', '=', 'cars.hash_id')
            ->where('anketas.company_id', $company)
            ->whereNotNull('anketas.car_id')
            ->where('anketas.in_cart', 0)
            ->where('is_dop', 0)
            ->whereBetween('anketas.date', [
                $date_from." 00:00:00",
                $date_to." 23:59:59"
            ])
            ->select('car_gos_number', 'car_id', 'driver_id', 'type_anketa', 'type_view', 'cars.products_id', DB::raw('count(*) as total'))
            ->groupBy(['car_id', 'type_view'])
            ->get();


        $techsTypeCounts = Anketa::whereIn('type_anketa', ['tech', 'bdd', 'report_cart'])
            ->whereIn('car_id', $techs->pluck('car_id')->toArray())
            ->join('cars', 'anketas.car_id', '=', 'cars.hash_id')
            ->where('anketas.company_id', $company)
            ->whereNotNull('anketas.car_id')
            ->where('anketas.in_cart', 0)
            ->where('is_dop', 0)
            ->whereBetween('anketas.date', [
                $date_from." 00:00:00",
                $date_to." 23:59:59"
            ])
            ->select('car_id', 'type_anketa', 'driver_id', 'cars.products_id', DB::raw('count(*) as total'))
            ->groupBy(['type_anketa', 'driver_id'])
            ->get();

        $result = [];
        foreach ($techsTypeCounts as $row) {
            $result[$row->driver_id]['types'][$row->type_anketa]['total'] = $row->total;
        }

        foreach ($techs as $row) {
            $result[$row->driver_id]['car_gos_number'] = $row->car_gos_number;
            $result[$row->driver_id]['types'][$row->type_view]['total'] = $row->total;

            $services = explode(',', $row->products_id);
            $prods = $products->whereIn('id', $services);

            if ($prods->count() > 0) {
                foreach ($prods as $service) {
                    $discounts = $discounts->where('products_id', $service->id);

                    if($discounts->count()) {
                        foreach($discounts as $discount) {
                            $service->price_unit = $discount->add($row->total, $service->price_unit);
                        }
                    }
                }

                $result[$row->driver_id]['types'][$row->type_view]['sum'] = $prods->sum('price_unit');

            }

        }

        return $result;
    }

    public function getJournalMedicsOther($company, $date_from, $date_to) {
        $reports = Anketa::whereIn('type_anketa', ['medic', 'bdd', 'report_cart'])
            ->where('company_id', $company)
            ->where('in_cart', 0)
            ->where('is_dop', 0)
            ->whereBetween('created_at', [
                $date_from." 00:00:00",
                $date_to." 23:59:59"
            ])
            ->whereNotBetween('date', [
                $date_from." 00:00:00",
                $date_to." 23:59:59"
            ])
            ->select('driver_id', 'type_view', 'driver_fio', 'date', 'type_anketa')
            ->get();

        $result = [];

        foreach ($reports as $report) {
            $date = Carbon::parse($report->date);
            $key = $date->year . '-' . $date->month; // key by date

            $result[$key]['year'] = $date->year;
            $result[$key]['month'] = $date->month;
            $result[$key]['reports'][$report->driver_id]['driver_fio'] = $report->driver_fio;
            $result[$key]['reports'][$report->driver_id]['types'][$report->type_view]['total']
                = $reports->where('driver_id', $report->driver_id)->where('type_view', $report->type_view)->count();
            $result[$key]['reports'][$report->driver_id]['types'][$report->type_anketa]['total']
                = $reports->where('driver_id', $report->driver_id)->where('type_anketa', $report->type_anketa)->count();
        }

        return array_reverse($result);
    }

    public function getJournalTechsOther($company, $date_from, $date_to) {
        $reports = Anketa::whereIn('type_anketa', ['tech', 'bdd', 'report_cart'])
            ->where('company_id', $company)
            ->whereNotNull('car_id')
            ->where('in_cart', 0)
            ->where('is_dop', 0)
            ->whereBetween('created_at', [
                $date_from." 00:00:00",
                $date_to." 23:59:59"
            ])
            ->whereNotBetween('date', [
                $date_from." 00:00:00",
                $date_to." 23:59:59"
            ])
            ->select('car_gos_number', 'car_id', 'date', 'type_anketa', 'type_view')
            ->get();

        $result = [];

        foreach ($reports as $report) {
            $date = Carbon::parse($report->date);
            $key = $date->year . '-' . $date->month; // key by date

            $result[$key]['year'] = $date->year;
            $result[$key]['month'] = $date->month;
            $result[$key]['reports'][$report->car_id]['car_gos_number'] = $report->car_gos_number;
            $result[$key]['reports'][$report->car_id]['types'][$report->type_view]['total']
                = $reports->where('car_id', $report->car_id)->where('type_view', $report->type_view)->count();
            $result[$key]['reports'][$report->car_id]['types'][$report->type_anketa]['total']
                = $reports->where('car_id', $report->car_id)->where('type_anketa', $report->type_anketa)->count();
        }

        return array_reverse($result);
    }

    public function getJournalPl($company, $date_from, $date_to) {
        $reports = Anketa::whereIn('type_anketa', ['medic', 'tech'])
            ->where('company_id', $company)
            ->where('in_cart', 0)
            ->where('is_dop', 1)
            ->whereBetween('created_at', [
                $date_from." 00:00:00",
                $date_to." 23:59:59"
            ])
            ->select('car_id', 'driver_id', 'car_gos_number', 'driver_fio', 'type_anketa',
                'date', 'type_view')
            ->get();

        $result = [];

        foreach ($reports as $report) {
            $date = Carbon::parse($report->date);
            $key = $date->year . '-' . $date->month; // key by date

            $result[$key]['year'] = $date->year;
            $result[$key]['month'] = $date->month;
            $result[$key]['reports'][$report->driver_id]['car_gos_number'] = $report->car_gos_number;
            $result[$key]['reports'][$report->driver_id]['driver_fio'] = $report->driver_fio;

            $reports = $result[$key]['reports'][$report->driver_id];
            $view_count = 0;
            $anketa_count = 0;
//            ['types'][$report->type_view]['total']
            if (key_exists('types', $reports)) {
                if (key_exists($report->type_view, $reports['types'])
                    && key_exists('total', $reports['types'][$report->type_view])) {
                    $type_count = $reports['types'][$report->type_view]['total'];
                }

                if (key_exists($report->type_anketa, $reports['types'])
                    && key_exists('total', $reports['types'][$report->type_anketa])) {
                    $type_count = $reports['types'][$report->type_anketa]['total'];
                }
            }

            $result[$key]['reports'][$report->driver_id]['types'][$report->type_view]['total'] = $view_count + 1;

            $result[$key]['reports'][$report->driver_id]['types'][$report->type_anketa]['total'] = $anketa_count + 1;
        }

        return array_reverse($result);
    }

    public function ApiGetReport(Request $request)
    {
        $report = $this->GetReport($request);

        return response()->json($report);
    }

}
