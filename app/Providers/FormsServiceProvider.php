<?php

namespace App\Providers;

use App\Enums\FeaturesEnum;
use App\Models\Forms\ActionsPolicy\Builders\ByStateBuilder;
use App\Models\Forms\ActionsPolicy\Builders\DisabledBuilder;
use App\Models\Forms\ActionsPolicy\Contracts\BuilderInterface;
use App\Services\Unleash\UnleashClient;
use Exception;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Unleash\Client\Configuration\UnleashContext;

final class FormsServiceProvider extends ServiceProvider
{
    /**
     * @throws Exception
     */
    public function register()
    {
        $this->app->bind(
            BuilderInterface::class,
            $this->getActionsPolicyFactoryImplementation()
        );
    }

    public function boot()
    {
        Blade::directive('disabled', function ($expression) {
            return "<?php echo filter_var($expression, FILTER_VALIDATE_BOOLEAN) ? 'disabled' : ''; ?>";
        });
    }

    /**
     * @throws Exception
     */
    private function getActionsPolicyFactoryImplementation(): string
    {
        $unleash = (new UnleashClient())->get();

        $context = (new UnleashContext())
            ->setHostname(config('unleash.hostname'));

        if ($unleash->isEnabled(FeaturesEnum::FORMS_BLOCK_ATTRIBUTES_UPDATING, $context)) {
            return ByStateBuilder::class;
        }

        return DisabledBuilder::class;
    }
}
