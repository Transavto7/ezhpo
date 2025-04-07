<?php

declare(strict_types=1);

namespace Src\Employees\Tariffs\Actions\UpdateTariff;

use Illuminate\Validation\ValidationException;
use Src\Employees\Tariffs\Serializers\PriceHourIntervalDBSerializer;
use Src\Employees\Tariffs\Validators\OverlapIntervalValidator;
use Src\Employees\Workdays\Eloquent\Tariff;
use Src\Employees\Workdays\Eloquent\TariffHour;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class UpdateTariffHandler
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

    public function handle(UpdateTariffAction $action): void
    {
        $tariff = Tariff::find($action->getTariffId());

        if ($tariff === null) {
            throw new NotFoundHttpException('Tariff not found');
        }

        if ($this->overlapIntervalValidator->validate(
            $action->getDateFrom(),
            $action->getDateTo(),
            $action->getTownId(),
            $action->getRoleId(),
            $action->getPointId(),
            $action->getTariffId()
        )) {
            throw ValidationException::withMessages([
                'interval' => $this->overlapIntervalValidator->getErrorMessage(),
            ]);
        }

        $tariff->fill([
            'name' => $action->getName(),
            'price_cfg' => $action->getPriceCfg(),
            'date_to' => $action->getDateTo(),
            'date_from' => $action->getDateFrom(),
            'point_id' => $action->getPointId(),
            'role_id' => $action->getRoleId(),
            'town_id' => $action->getTownId(),
        ]);

        $tariff->save();

        $serializedHours = $this->serializer->serialize(
            $action->getCustomHours(),
            $action->getDefaultPrice(),
            $tariff->id
        );

        TariffHour::query()->where('tariff_id', $tariff->id)->delete();
        TariffHour::query()->insert($serializedHours);
    }
}
