<?php

declare(strict_types=1);

namespace Src\Employees\Tariffs\Queries\GetTariff;

use DateTimeImmutable;
use Src\Core\Constants\DateFormats;
use Src\Core\ValueObjects\ClassifierViewModel;
use Src\Employees\Tariffs\Serializers\PriceHourIntervalDBSerializer;
use Src\Employees\Workdays\Eloquent\Tariff;
use Src\Employees\Workdays\Eloquent\TariffHour;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class GetTariffHandler
{
    /** @var PriceHourIntervalDBSerializer */
    private $serializer;

    /**
     * @param PriceHourIntervalDBSerializer $serializer
     */
    public function __construct(PriceHourIntervalDBSerializer $serializer)
    {
        $this->serializer = $serializer;
    }

    public function handle(GetTariffQuery $query): TariffViewModel
    {
        $tariff = Tariff::with(['town', 'pv', 'role'])->find($query->getTariffId());

        if ($tariff === null) {
            throw new NotFoundHttpException('Tariff not found');
        }

        $intervalModels = TariffHour::query()->where('tariff_id', $query->getTariffId())->get()->all();
        $deserializeIntervals = $this->serializer->deserialize($intervalModels);

        return new TariffViewModel(
            $tariff->id,
            $tariff->name,
            $tariff->price_cfg,
            DateTimeImmutable::createFromFormat(DateFormats::SYSTEM_DATE, $tariff->date_from->format(DateFormats::SYSTEM_DATE)),
            DateTimeImmutable::createFromFormat(DateFormats::SYSTEM_DATE, $tariff->date_to->format(DateFormats::SYSTEM_DATE)),
            $deserializeIntervals->getDefaultPrice(),
            $deserializeIntervals->getIntervals(),
            new ClassifierViewModel(
                $tariff->town->id,
                $tariff->town->name,
            ),
            new ClassifierViewModel(
                $tariff->role->id,
                $tariff->role->guard_name,
            ),
            $tariff->pv ? new ClassifierViewModel(
                $tariff->pv->id,
                $tariff->pv->name,
            ) : null,
        );
    }
}
