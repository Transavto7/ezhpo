<?php

namespace App\Actions\Reports\Journal\GetJournalData;

use App\Anketa;
use App\Car;
use App\Company;
use App\Discount;
use App\Driver;
use App\Product;
use Carbon\Carbon;
use Exception;

class GetJournalDataHandler
{
    public function handle(GetJournalDataAction $action)
    {
        $company = Company::query()
            ->select([
                'id',
                'hash_id',
                'name',
                'products_id'
            ])
            ->where('hash_id', $action->getCompanyHashId())
            ->first();

        $products = Product::all();
        $discounts = Discount::all();

        return [
            'medics' => $this->getJournalMedic($company, $action->getDateFrom(), $action->getDateTo(), $products, $discounts),
            'techs' => $this->getJournalTechs($company, $action->getDateFrom(), $action->getDateTo(), $products, $discounts),
            'medics_other' => $this->getJournalMedicsOther($company, $action->getDateFrom(), $action->getDateTo(), $products, $discounts),
            'techs_other' => $this->getJournalTechsOther($company, $action->getDateFrom(), $action->getDateTo(), $products, $discounts),
            'other' => $this->getJournalOther($company, $products),
        ];
    }

    public function getJournalMedic($company, $date_from, $date_to, $products, $discounts): array
    {
        $medics = Anketa::query()
            ->select([
                'driver_fio',
                'driver_id',
                'type_anketa',
                'type_view',
                'result_dop',
                'products_id',
                'pv_id',
                'is_dop',
                'anketas.count_pl',
                'admitted'
            ])
            ->whereIn('type_anketa', ['medic', 'bdd', 'report_cart', 'pechat_pl'])
            ->leftJoin('drivers', 'anketas.driver_id', '=', 'drivers.hash_id')
            ->where(function ($query) use ($company) {
                $query->where('anketas.company_id', $company->hash_id)
                    ->orWhere('anketas.company_name', $company->name);
            })
            ->where('anketas.in_cart', 0)
            ->where(function ($q) use ($date_from, $date_to) {
                $q
                    ->where(function ($q) use ($date_from, $date_to) {
                        $q->whereNotNull('anketas.date')
                            ->whereBetween('anketas.date', [
                                $date_from,
                                $date_to,
                            ]);
                    })
                    ->orWhere(function ($q) use ($date_from, $date_to) {
                        $q->whereNull('anketas.date')
                            ->whereBetween('anketas.period_pl', [
                                $date_from->format('Y-m'),
                                $date_to->format('Y-m'),
                            ]);
                    });
            })
            ->get();

        $result = [];

        foreach ($medics->groupBy('driver_id') as $driver) {
            $id = $driver->first()->driver_id;
            $driver_fio = $driver->where('driver_fio', '!=', null)->first();
            $result[$id]['driver_fio'] = $driver_fio ? $driver_fio->driver_fio : null;

            $result[$id]['pv_id'] = implode('; ', array_unique($driver->pluck('pv_id')->toArray()));

            foreach ($driver->where('type_anketa', 'medic')->where('admitted', '!=', 'Не идентифицирован')->groupBy('type_view') as $rows) {
                $type = $rows->first()->type_view;
                $total = $rows->count();
                $result[$id]['types'][$type]['total'] = $total;

                if ($id == null) {
                    $services = explode(',', $company->products_id);
                } else {
                    $services = explode(',', $driver->first()->products_id);
                }

                $types = explode('/', $type);
                $prods = $products->whereIn('id', $services)->where('type_anketa', 'medic');


                if ($prods->count() > 0) {
                    foreach ($prods as $service) {
                        $disc = $discounts->where('products_id', $service->id);
                        $service->price = $service->price_unit;

                        if ($disc->count()) {
                            foreach ($disc as $discount) {
                                $disSum = $discount->getDiscount($total);
                                if ($disSum) {
                                    $service->price = $service->price_unit - ($service->price_unit * $disSum / 100);
                                    $result[$id]['types'][$type]['discount'] = 1 * $disSum;
                                }
                            }
                        }

                        $vt = $service->type_view;

                        foreach ($types as $type_view) {
                            if (strpos($vt, $type_view) !== false) {
                                $result[$id]['types'][$type]['sync'] =
                                    in_array($service->id, explode(',', $company->products_id));

                                $result[$id]['types'][$type]['name'] = $service->name;
                                if ($service->type_product === 'Разовые осмотры') {
                                    $result[$id]['types'][$type]['sum'] = $service->price * $total;
                                } else {
                                    $result[$id]['types'][$type]['sum'] = $service->price;
                                }
                            }
                        }
                    }
                }
            }


            foreach ($driver->groupBy(['type_anketa']) as $rows) {
                $type = $rows->first()->type_anketa;
                if ($type === 'pechat_pl') {
                    $total = $rows->sum('count_pl');
                } else {
                    $total = $rows->count();
                }
                $result[$id]['types'][$type]['total'] = $total;

                $services = explode(',', $driver->first()->products_id);
                $prods = $products->whereIn('id', $services)->where('type_anketa', $type);

                if ($prods->count() > 0) {
                    foreach ($prods as $service) {
                        $disc = $discounts->where('products_id', $service->id);
                        $service->price = $service->price_unit;

                        if ($disc->count()) {
                            foreach ($disc as $discount) {
                                $disSum = $discount->getDiscount($total);
                                if ($disSum) {
                                    $service->price = $service->price_unit - ($service->price_unit * $disSum / 100);
                                    $result[$id]['types'][$type]['discount'] = 1 * $disSum;
                                }
                            }
                        }

                        $result[$id]['types'][$type]['sync'] =
                            in_array($service->id, explode(',', $company->products_id));

                        if ($service->type_product === 'Разовые осмотры') {
                            $result[$id]['types'][$type]['sum'] = $service->price * $total;
                        } else {
                            $result[$id]['types'][$type]['sum'] = $service->price;
                        }
                    }
                }
            }

            $result[$id]['types']['is_dop']['total'] = $driver
                ->where('type_anketa', 'medic')
                ->where('result_dop', null)
                ->where('is_dop', 1)
                ->count();

            $result[$id]['types']['Не идентифицирован'] = [
                'total' => $driver
                    ->where('type_anketa', 'medic')
                    ->where('admitted', 'Не идентифицирован')
                    ->count()
            ];
        }

        return $result;
    }

    public function getJournalTechs($company, $date_from, $date_to, $products, $discounts): array
    {
        $techs = Anketa::query()
            ->select(['car_gos_number', 'car_id', 'type_auto', 'type_anketa', 'is_dop', 'result_dop', 'pv_id',
                'type_view', 'products_id'])
            ->where('type_anketa', 'tech')
            ->leftJoin('cars', 'anketas.car_id', '=', 'cars.hash_id')
            ->where(function ($query) use ($company) {
                $query->where('anketas.company_id', $company->hash_id)
                    ->orWhere('anketas.company_name', $company->name);
            })
            ->where('anketas.in_cart', 0)
            ->where(function ($q) use ($date_from, $date_to) {
                $q->where(function ($q) use ($date_from, $date_to) {
                    $q->whereNotNull('anketas.date')
                        ->whereBetween('anketas.date', [
                            $date_from,
                            $date_to,
                        ]);
                })
                    ->orWhere(function ($q) use ($date_from, $date_to) {
                        $q->whereNull('anketas.date')->whereBetween('anketas.period_pl', [
                            $date_from->format('Y-m'),
                            $date_to->format('Y-m'),
                        ]);
                    });
            })
            ->get();

        $result = [];

        foreach ($techs->groupBy('car_id') as $car) {
            $id = $car->first()->car_id;
            $numberCar = $car->where('car_gos_number', '!=', null)->first();
            $typeCar = $car->where('type_auto', '!=', null)->first();
            $result[$id]['car_gos_number'] = $numberCar ? $numberCar->car_gos_number : null;
            $result[$id]['type_auto'] = $typeCar ? $typeCar->type_auto : null;
            $result[$id]['pv_id'] = implode('; ', array_unique($car->pluck('pv_id')->toArray()));

            foreach ($car->groupBy(['type_view']) as $rows) {
                $type = $rows->first()->type_view;
                $total = $rows->count();
                $result[$id]['types'][$type]['total'] = $total;

                if ($id == null) {
                    $services = explode(',', $company->products_id);
                } else {
                    $services = explode(',', $car->first()->products_id);
                }

                $types = explode('/', $type);
                $prods = $products->whereIn('id', $services)->where('type_anketa', 'tech');

                if ($prods->count() > 0) {
                    foreach ($prods as $service) {
                        $disc = $discounts->where('products_id', $service->id);
                        $service->price = $service->price_unit;

                        if ($disc->count()) {
                            foreach ($disc as $discount) {
                                $disSum = $discount->getDiscount($total);
                                if ($disSum) {
                                    $service->price = $service->price_unit - ($service->price_unit * $disSum / 100);
                                    $result[$id]['types'][$type]['discount'] = 1 * $disSum;
                                }
                            }
                        }

                        $vt = $service->type_view;

                        foreach ($types as $type_view) {
                            if (strpos($vt, $type_view) !== false) {
                                $result[$id]['types'][$type]['sync'] =
                                    in_array($service->id, explode(',', $company->products_id));

                                if ($service->type_product === 'Разовые осмотры') {
                                    $result[$id]['types'][$type]['sum'] = $service->price * $total;
                                } else {
                                    $result[$id]['types'][$type]['sum'] = $service->price;
                                }
                            }
                        }
                    }
                }
            }

            $result[$id]['types']['is_dop']['total'] = $car->where('type_anketa', 'tech')
                ->where('result_dop', null)->where('is_dop', 1)->count();
        }

        return $result;
    }

    public function getJournalMedicsOther($company, $date_from, $date_to, $products, $discounts)
    {
        $reports = Anketa::whereIn('type_anketa', ['medic', 'bdd', 'report_cart', 'pechat_pl'])
            ->leftJoin('drivers', 'anketas.driver_id', '=', 'drivers.hash_id')
            ->where(function ($query) use ($company) {
                $query->where('anketas.company_id', $company->hash_id)
                    ->orWhere('anketas.company_name', $company->name);
            })
            ->where('in_cart', 0)
            ->whereBetween('anketas.created_at', [
                $date_from,
                $date_to
            ])
            ->where(function ($q) use ($date_from, $date_to) {
                $q->where(function ($q) use ($date_from, $date_to) {
                    $q->whereNotNull('anketas.date')
                        ->whereNotBetween('anketas.date', [
                            $date_from,
                            $date_to,
                        ]);
                })
                    ->orWhere(function ($q) use ($date_from, $date_to) {
                        $q->whereNull('anketas.date')->whereNotBetween('anketas.period_pl', [
                            $date_from->format('Y-m'),
                            $date_to->format('Y-m'),
                        ]);
                    });
            })
            ->select('driver_id', 'period_pl', 'type_view', 'driver_fio', 'date', 'is_dop', 'pv_id',
                'products_id', 'result_dop', 'type_anketa')
            ->get();

        $result = [];

        foreach ($reports as $report) {
            try {
                if ($report->date) {
                    $date = Carbon::parse($report->date);
                } else {
                    $date = Carbon::parse($report->period_pl);
                }
            } catch (Exception $e) {
                continue;
            }
            $key = $date->year . '-' . $date->month; // key by date

            $result[$key]['year'] = $date->year;
            $result[$key]['month'] = $date->month;
            $result[$key]['reports'][$report->driver_id]['driver_fio'] = $report->driver_fio;
            $result[$key]['reports'][$report->driver_id]['pv_id'] = implode('; ',
                array_unique($reports->where('driver_id', $report->driver_id)->pluck('pv_id')->toArray()));

            $total = $result[$key]['reports'][$report->driver_id]['types'][$report->type_view]['total'] =
                ($result[$key]['reports'][$report->driver_id]['types'][$report->type_view]['total'] ?? 0) + 1;

            $result[$key]['reports'][$report->driver_id]['types'][$report->type_anketa]['total'] =
                ($result[$key]['reports'][$report->driver_id]['types'][$report->type_anketa]['total'] ?? 0) + 1;

            if ($report->is_dop && $report->result_dop == null) {
                $result[$key]['reports'][$report->driver_id]['types']['is_dop']['total'] =
                    ($result[$key]['reports'][$report->driver_id]['types']['is_dop']['total'] ?? 0) + 1;
            }

            if ($report->driver_id == null) {
                $services = explode(',', $company->products_id);
            } else {
                $services = explode(',', $report->products_id);
            }

            $types = explode('/', $report->type_view);
            $prods = $products->whereIn('id', $services);

            if ($prods->count() > 0) {
                foreach ($prods as $service) {
                    $disc = $discounts->where('products_id', $service->id);
                    $service->price = $service->price_unit;
                    $service->discount = 0;

                    if ($disc->count()) {
                        foreach ($disc as $discount) {
                            $disSum = $discount->getDiscount($total);
                            if ($disSum) {
                                $service->price = $service->price_unit - ($service->price_unit * $disSum / 100);
                                $service->discount = 1 * $disSum;
                            }
                        }
                    }

                    if ($service->type_anketa === 'medic') {
                        $vt = $service->type_view;

                        foreach ($types as $type_view) {
                            if (strpos($vt, $type_view) !== false) {
                                $result[$key]['reports'][$report->driver_id]['types'][$report->type_view]['sync'] =
                                    in_array($service->id, explode(',', $company->products_id));

                                if ($service->type_product === 'Разовые осмотры') {
                                    $result[$key]['reports'][$report->driver_id]['types'][$report->type_view]['sum'] = $service->price * $total;
                                } else {
                                    $result[$key]['reports'][$report->driver_id]['types'][$report->type_view]['sum'] = $service->price;
                                }

                                if ($service->discount) {
                                    $result[$key]['reports'][$report->driver_id]['types'][$report->type_view]['discount'] = $service->discount;
                                }
                            }
                        }
                    } else if (isset($result[$key]['reports'][$report->driver_id]['types'][$service->type_anketa])) {
                        $result[$key]['reports'][$report->driver_id]['types'][$service->type_anketa]['sync'] =
                            in_array($service->id, explode(',', $company->products_id));

                        if ($service->type_product === 'Разовые осмотры') {
                            $result[$key]['reports'][$report->driver_id]['types'][$service->type_anketa]['sum'] = $service->price * $total;
                        } else {
                            $result[$key]['reports'][$report->driver_id]['types'][$service->type_anketa]['sum'] = $service->price;
                        }

                        if ($service->discount) {
                            $result[$key]['reports'][$report->driver_id]['types'][$service->type_anketa]['discount'] = $service->discount;
                        }
                    }
                }
            }
        }

        return array_reverse($result);
    }

    public function getJournalTechsOther($company, $date_from, $date_to, $products, $discounts): array
    {
        $reports = Anketa::whereIn('type_anketa', ['tech', 'bdd', 'type_anketa', 'pechat_pl'])
            ->leftJoin('cars', 'anketas.car_id', '=', 'cars.hash_id')
            ->where(function ($query) use ($company) {
                $query->where('anketas.company_id', $company->hash_id)
                    ->orWhere('anketas.company_name', $company->name);
            })
            ->where('in_cart', 0)
            ->whereBetween('anketas.created_at', [
                $date_from,
                $date_to
            ])
            ->where(function ($q) use ($date_from, $date_to) {
                $q->where(function ($q) use ($date_from, $date_to) {
                    $q->whereNotNull('anketas.date')
                        ->whereNotBetween('anketas.date', [
                            $date_from,
                            $date_to,
                        ]);
                })
                    ->orWhere(function ($q) use ($date_from, $date_to) {
                        $q->whereNull('anketas.date')->whereNotBetween('anketas.period_pl', [
                            $date_from->format('Y-m'),
                            $date_to->format('Y-m'),
                        ]);
                    });
            })
            ->select('anketas.car_gos_number', 'type_auto', 'period_pl', 'car_id', 'date', 'result_dop',
                'type_anketa', 'is_dop',
                'pv_id', 'products_id', 'type_view')
            ->get();

        $result = [];

        foreach ($reports as $report) {
            try {
                if ($report->date) {
                    $date = Carbon::parse($report->date);
                } else {
                    $date = Carbon::parse($report->period_pl);
                }
            } catch (Exception $e) {
                continue;
            }
            $key = $date->year . '-' . $date->month; // key by date

            $result[$key]['year'] = $date->year;
            $result[$key]['month'] = $date->month;
            $result[$key]['reports'][$report->car_id]['car_gos_number'] = $report->car_gos_number;
            $result[$key]['reports'][$report->car_id]['type_auto'] = $report->type_auto;
            $result[$key]['reports'][$report->car_id]['pv_id'] = implode('; ',
                array_unique($reports->where('car_id', $report->car_id)->pluck('pv_id')->toArray()));

            $total = $result[$key]['reports'][$report->car_id]['types'][$report->type_view]['total']
                = ($result[$key]['reports'][$report->car_id]['types'][$report->type_view]['total'] ?? 0) + 1;

            if ($report->is_dop && $report->result_dop == null) {
                $result[$key]['reports'][$report->car_id]['types']['is_dop']['total']
                    = ($result[$key]['reports'][$report->car_id]['types']['is_dop']['total'] ?? 0) + 1;
            }

            if ($report->products_id == null) {
                $services = explode(',', $company->products_id);
            } else {
                $services = explode(',', $report->products_id);
            }

            $types = explode('/', $report->type_view);
            $prods = $products->whereIn('id', $services);

            if ($prods->count() > 0) {
                foreach ($prods as $service) {
                    $disc = $discounts->where('products_id', $service->id);
                    $service->price = $service->price_unit;
                    $service->discount = 0;

                    if ($disc->count()) {
                        foreach ($disc as $discount) {
                            $disSum = $discount->getDiscount($total);
                            if ($disSum) {
                                $service->price = $service->price_unit - ($service->price_unit * $disSum / 100);
                                $service->discount = 1 * $disSum;
                            }
                        }
                    }

                    if ($service->type_anketa === 'tech') {
                        $vt = $service->type_view;

                        foreach ($types as $type_view) {
                            if (strpos($vt, $type_view) !== false) {
                                $result[$key]['reports'][$report->car_id]['types'][$report->type_view]['sync'] =
                                    in_array($service->id, explode(',', $company->products_id));

                                if ($service->type_product === 'Разовые осмотры') {
                                    $result[$key]['reports'][$report->car_id]['types'][$report->type_view]['sum'] = $service->price * $total;
                                } else {
                                    $result[$key]['reports'][$report->car_id]['types'][$report->type_view]['sum'] = $service->price;
                                }

                                if ($service->discount) {
                                    $result[$key]['reports'][$report->car_id]['types'][$report->type_view]['discount'] = $service->discount;
                                }
                            }
                        }
                    }
                }
            }
        }

        return array_reverse($result);
    }

    public function getJournalOther($company, $products): array
    {
        $result = [];
        $companyProdsID = explode(',', $company->products_id);
        $prods = $products->where('type_product', 'Абонентская плата без реестров');
        $drivers = Driver::where('company_id', $company->id)->get();
        $cars = Car::where('company_id', $company->id)->get();

        foreach ($prods->whereIn('id', $companyProdsID)->where('essence', 0) as $product) {
            $result['company'][$product->name] = $product->price_unit;
        }

        foreach ($drivers as $driver) {
            $driverProdsID = explode(',', $driver->products_id);
            foreach ($prods->whereIn('id', $driverProdsID)->whereIn('essence', [1, 3]) as $product) {
                $result['drivers'][] = [
                    'driver_fio' => $driver->fio,
                    'name' => $product->name,
                    'sum' => 1 * $product->price_unit
                ];
            }
        }

        foreach ($cars as $car) {
            $carProdsID = explode(',', $car->products_id);
            foreach ($prods->whereIn('id', $carProdsID)->whereIn('essence', [2, 3]) as $product) {
                $result['cars'][] = [
                    'gos_number' => $car->gos_number,
                    'type_auto' => $car->type_auto,
                    'name' => $product->name,
                    'sum' => 1 * $product->price_unit
                ];
            }
        }

        return $result;
    }
}
