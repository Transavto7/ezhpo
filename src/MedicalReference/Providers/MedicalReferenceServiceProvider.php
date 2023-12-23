<?php

namespace Src\MedicalReference\Providers;

use Illuminate\Support\ServiceProvider;
use Src\MedicalReference\Services\MedicalReferenceExporter;
use Src\MedicalReference\Services\MedicalReferenceExporterInterface;

class MedicalReferenceServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(MedicalReferenceExporterInterface::class, MedicalReferenceExporter::class);
    }

    public function boot()
    {
        $this->loadRoutesFrom(base_path('src/MedicalReference/Http/medical-reference.php'));
        $this->loadViewsFrom(base_path('src/MedicalReference/Views'), 'medical-reference');
    }
}
