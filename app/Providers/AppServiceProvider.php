<?php

namespace App\Providers;

use App\Anketa;
use App\Car;
use App\Company;
use App\Driver;
use App\Models\Contract;
use App\Observers\AnketaObserver;
use App\Observers\CompanyObserver;
use App\Observers\ContractObserver;
use App\Observers\DriverObserver;
use App\Observers\CarObserver;
use App\Observers\UserObserver;
use App\Services\ElementsSearch\ElementSearchService;
use App\Services\ElementsSearch\ElementsSearchServiceInterface;
use App\Services\HashIdGenerator\HashIdGeneratorService;
use App\Services\QRCode\QRCodeGenerator;
use App\Services\QRCode\QRCodeGeneratorInterface;
use App\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('hash-id-generator', function () {
            return new HashIdGeneratorService();
        });

        $this->app->bind(ElementsSearchServiceInterface::class, ElementSearchService::class);
        $this->app->bind(QRCodeGeneratorInterface::class, QRCodeGenerator::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        $this->registerModelObservers();
    }

    private function registerModelObservers()
    {
        Car::observe(CarObserver::class);
        Company::observe(CompanyObserver::class);
        Contract::observe(ContractObserver::class);
        Driver::observe(DriverObserver::class);
        User::observe(UserObserver::class);
        Anketa::observe(AnketaObserver::class);
    }
}
