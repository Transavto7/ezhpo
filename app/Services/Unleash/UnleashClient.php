<?php

namespace App\Services\Unleash;

use Throwable;
use Unleash\Client\Unleash;
use Unleash\Client\UnleashBuilder;

class UnleashClient
{
    /**
     * @var Unleash
     */
    private $unleash;

    public function __construct()
    {
        $this->unleash = new OfflineUnleash();

        if (config('unleash.enabled')) {
            try {
                $this->unleash = UnleashBuilder::create()
                    ->withAppName(config('unleash.app-name'))
                    ->withAppUrl(config('unleash.app-url'))
                    ->withInstanceId(config('unleash.instance-id'))
                    ->withHeader('Authorization', config('unleash.token'))
                    ->withInstanceId(config('unleash.instance-id'))
                    ->build();
            } catch (Throwable $exception) {

            }
        }
    }

    public function get(): Unleash
    {
        return $this->unleash;
    }
}
