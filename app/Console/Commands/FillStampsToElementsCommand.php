<?php

namespace App\Console\Commands;

use App\Point;
use App\Town;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FillStampsToElementsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stamps:fill
                            {--force : Запуск команды с затиранием не нулевых значений}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Установка штампов по-умолчанию для элементов CRM';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            DB::beginTransaction();

            $this->restoreForPoints();
            $this->restoreForTowns();

            DB::commit();
        } catch (\Throwable $exception) {
            $this->log("Ошибка: {$exception->getMessage()}");

            DB::rollBack();
        }
    }

    private function restoreForPoints()
    {
        $this->log("Восстановление штампов для ПВ!");

        $stampsOnElements = DB::table('users')
            ->select([
                DB::raw('count(users.id) as count'),
                'users.stamp_id',
                'points.id as element_id'
            ])
            ->leftJoin('model_has_roles', 'model_has_roles.model_id', '=','users.id')
            ->leftJoin('points', 'users.pv_id', '=', 'points.id')
            ->where('model_has_roles.role_id', 9)
            ->whereNull('users.deleted_at')
            ->whereNull('points.deleted_at')
            ->whereNotNull('points.id')
            ->whereNotNull('users.stamp_id')
            ->groupBy(['users.stamp_id', 'points.id'])
            ->get()
            ->toArray();

        $stamps = $this->getStampsForElements($stampsOnElements);

        $elementsQuery = Point::query();

        if (!$this->option('force')) {
            $this->log("Только для незаполненных элементов!");
            $elementsQuery->whereNull('stamp_id');
        }

        $elementsQuery->get()->each(function ($element) use ($stamps) {
            $this->updateElement($element, $stamps);
        });
    }

    private function updateElement($element, $stamps)
    {
        if (!isset($stamps[$element->id])) {
            return;
        }

        if ($stamps[$element->id]['has_duplicates']) {
            return;
        }

        $element->update(['stamp_id' => $stamps[$element->id]['stamp_id']]);

        $this->log("Установка ID штампа {$stamps[$element->id]['stamp_id']} для {$element->name}");
    }

    private function getStampsForElements(array $raw): array
    {
        $stamps = [];

        foreach ($raw as $stampsOnElement) {
            $elementId = $stampsOnElement->element_id;
            $stampId = $stampsOnElement->stamp_id;
            $count = $stampsOnElement->count;

            if (!isset($stamps[$elementId])) {
                $stamps[$elementId] = [
                    'stamp_id' => $stampId,
                    'count' => $count,
                    'has_duplicates' => false
                ];

                continue;
            }

            $currentPointCount = $stamps[$elementId]['count'];

            if ($currentPointCount > $count) {
                continue;
            }

            if ($currentPointCount === $count) {
                $stamps[$elementId]['has_duplicates'] = true;

                continue;
            }

            $stamps[$elementId] = [
                'stamp_id' => $stampId,
                'count' => $count,
                'has_duplicates' => false
            ];
        }

        return $stamps;
    }

    private function restoreForTowns()
    {
        $this->log("Восстановление штампов для городов!");

        $stampsOnElements = DB::table('points')
            ->select([
                DB::raw('count(points.id) as count'),
                'points.stamp_id',
                'towns.id as element_id'
            ])
            ->leftJoin('towns', 'points.pv_id', '=', 'towns.id')
            ->whereNull('points.deleted_at')
            ->whereNull('towns.deleted_at')
            ->whereNotNull('towns.id')
            ->whereNotNull('points.stamp_id')
            ->groupBy(['points.stamp_id', 'towns.id'])
            ->get()
            ->toArray();

        $stamps = $this->getStampsForElements($stampsOnElements);

        $elementsQuery = Town::query();

        if (!$this->option('force')) {
            $this->log("Только для незаполненных элементов!");
            $elementsQuery->whereNull('stamp_id');
        }

        $elementsQuery->get()->each(function ($element) use ($stamps) {
            $this->updateElement($element, $stamps);
        });
    }

    private function log(string $message)
    {
        $this->info($message);
        Log::info($message);
    }


}
