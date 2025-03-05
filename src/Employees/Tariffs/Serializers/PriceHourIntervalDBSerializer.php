<?php

declare(strict_types=1);

namespace Src\Employees\Tariffs\Serializers;

use Src\Employees\Tariffs\ValueObjects\DeseriasizedIntervals;
use Src\Employees\Tariffs\ValueObjects\PriceHourInterval;
use Src\Employees\Workdays\Eloquent\TariffHour;

final class PriceHourIntervalDBSerializer
{
    /**
     * @param array<PriceHourInterval> $intervals
     * @param int $defaultPrice
     * @param int $tariffId
     * @return array
     */
    public function serialize(array $intervals, int $defaultPrice, int $tariffId): array
    {
        $hours = [];
        foreach (range(0, 23) as $hour) {
            $hours[$hour] = [
                'price' => $defaultPrice,
                'tariff_id' => $tariffId,
                'hour' => $hour,
            ];
        }

        foreach ($intervals as $interval) {
            foreach (range($interval->getStart(), $interval->getEnd()) as $hour) {
                $hours[$hour]['price'] = $interval->getPrice();
            }
        }

        return $hours;
    }

    /**
     * @param array<TariffHour> $data
     * @return DeseriasizedIntervals
     */
    public function deserialize(array $data): DeseriasizedIntervals
    {
        if (empty($data)) {
            return new DeseriasizedIntervals([], 0);
        }

        $priceCounts = [];
        foreach ($data as $tariffHour) {
            if (! isset($priceCounts[$tariffHour->price])) {
                $priceCounts[$tariffHour->price] = 0;
            }
            $priceCounts[$tariffHour->price]++;
        }

        arsort($priceCounts);
        $defaultPrice = array_key_first($priceCounts);

        usort($data, function ($a, $b) {
            return $a->hour <=> $b->hour;
        });

        $intervals = [];
        $start = null;
        $currentPrice = null;

        foreach ($data as $index => $tariffHour) {
            if ($tariffHour->price === $defaultPrice) {
                if ($start !== null) {
                    $intervals[] = new PriceHourInterval($start, $data[$index - 1]->hour, $currentPrice);
                    $start = null;
                    $currentPrice = null;
                }
                continue;
            }

            if ($start === null) {
                $start = $tariffHour->hour;
                $currentPrice = $tariffHour->price;
            } elseif ($tariffHour->price !== $currentPrice) {
                $intervals[] = new PriceHourInterval($start, $data[$index - 1]->hour, $currentPrice);
                $start = $tariffHour->hour;
                $currentPrice = $tariffHour->price;
            }
        }

        if ($start !== null) {
            $intervals[] = new PriceHourInterval($start, $data[array_key_last($data)]->hour, $currentPrice);
        }

        return new DeseriasizedIntervals($intervals, $defaultPrice);
    }
}
