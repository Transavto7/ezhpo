<?php

declare(strict_types=1);

namespace Src\Employees\Tariffs\Actions\CreateTariff;

use Illuminate\Validation\ValidationException;
use Src\Employees\Tariffs\Serializers\PriceHourIntervalDBSerializer;
use Src\Employees\Tariffs\Validators\OverlapIntervalValidator;
use Src\Employees\Workdays\Eloquent\Tariff;
use Src\Employees\Workdays\Eloquent\TariffHour;

final class CreateTariffHandler
{
    /** @var PriceHourIntervalDBSerializer */
    private $serializer;

    /** @var OverlapIntervalValidator */
    private $overlapIntervalValidator;

    /**
     * @param PriceHourIntervalDBSerializer $serializer
     * @param OverlapIntervalValidator $overlapIntervalValidator
     */
    public function __construct(PriceHourIntervalDBSerializer $serializer, OverlapIntervalValidator $overlapIntervalValidator)
    {
        $this->serializer = $serializer;
        $this->overlapIntervalValidator = $overlapIntervalValidator;
    }

    /**
     * @throws ValidationException
     */
    public function handle(CreateTariffAction $action): void
    {
        if ($this->overlapIntervalValidator->validate(
            $action->getDateFrom(),
            $action->getDateTo(),
            $action->getTownId(),
            $action->getRoleId(),
            $action->getPointId()
        )) {
            throw ValidationException::withMessages([
                'interval' => $this->overlapIntervalValidator->getErrorMessage(),
            ]);
        }

        $tariff = Tariff::create([
            'name' => $action->getName(),
            'price_cfg' => $action->getPriceCfg(),
            'date_to' => $action->getDateTo(),
            'date_from' => $action->getDateFrom(),
            'point_id' => $action->getPointId(),
            'role_id' => $action->getRoleId(),
            'town_id' => $action->getTownId(),
        ]);

        $serializedHours = $this->serializer->serialize(
            $action->getCustomHours(),
            $action->getDefaultPrice(),
            $tariff->id
        );

        TariffHour::query()->insert($serializedHours);
    }
}
