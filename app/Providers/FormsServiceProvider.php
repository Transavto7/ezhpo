<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

final class FormsServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Blade::directive('disabled', function ($expression) {
            return "<?php echo filter_var($expression, FILTER_VALIDATE_BOOLEAN) ? 'disabled' : ''; ?>";
        });
    }
}
