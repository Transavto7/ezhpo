<?php

namespace App\Console\Commands;

use App\Anketa;
use App\Car;
use App\Company;
use App\Driver;
use App\Enums\FormFixStatusEnum;
use App\GenerateHashIdTrait;
use App\Point;
use Illuminate\Console\Command;
use Ramsey\Uuid\Uuid;

class FixFormDataCommand extends Command
{
    use GenerateHashIdTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forms:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Фикс данных осмотров';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $nonFixedForms = Anketa::query()
            ->where('in_cart', 0)
            ->where('fix_status', FormFixStatusEnum::UNPROCESSED)->count();
        $this->info("Всего необработанных осмотров - $nonFixedForms");

        Anketa::query()
            ->where('in_cart', 0)
            ->where('fix_status', FormFixStatusEnum::UNPROCESSED)
            ->orderBy('created_at', 'DESC')
            ->chunk(1000, function ($forms) {
                foreach ($forms as $form) {
                    $status = FormFixStatusEnum::FIXED;

                    if (!$form->point_id) {
                        if ($form->pv_id) {
                            $point = Point::query()->where('name', $form->pv_id)->first();
                            if ($point) {
                                $form->point_id = $point->id;
                            } else {
                                $status = FormFixStatusEnum::INVALID_POINT_ID;
                            }
                        } else {
                            $status = FormFixStatusEnum::INVALID_POINT_ID;
                        }
                    }

                    if (!$form->company_id && $form->company_name) {
                        $company = Company::query()->where('name', $form->company_name)->first();
                        if ($company) {
                            $form->company_id = $company->hash_id;
                        }
                    }

                    if (!$form->company_id && $form->driver_id) {
                        $driver = Driver::query()->where('hash_id', $form->driver_id)->first();
                        if ($driver) {
                            $form->company_id = $driver->company_id;
                        } else {
                            $status = FormFixStatusEnum::INVALID_DRIVER_ID;
                        }
                    }

                    if (!$form->company_id && $form->car_id) {
                        $car = Car::query()->where('hash_id', $form->car_id)->first();
                        if ($car) {
                            $company = Company::query()->where('id', $driver->company_id)->first();
                            if ($company) {
                                $form->company_id = $company->hash_id;
                            }
                        } else {
                            $status = FormFixStatusEnum::INVALID_CAR_ID;
                        }
                    }

                    if (!$form->company_id) {
                        $status = FormFixStatusEnum::INVALID_COMPANY_ID;
                    }

                    $form->uuid = Uuid::uuid4();
                    $form->fix_status = $status;

                    $form->save();
                }
                $this->info('Обработано 1000 осмотров');
            });
    }
}
