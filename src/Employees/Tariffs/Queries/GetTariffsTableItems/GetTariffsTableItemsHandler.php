<?php

declare(strict_types=1);

namespace Src\Employees\Tariffs\Queries\GetTariffsTableItems;

use Src\Core\Constants\DateFormats;
use Src\Core\ValueObjects\TableItems;
use Src\Employees\Workdays\Eloquent\Tariff;

final class GetTariffsTableItemsHandler
{
    /** @var TariffsTableFiltersFactory */
    private $filtersFactory;

    /**
     * @param TariffsTableFiltersFactory $filtersFactory
     */
    public function __construct(TariffsTableFiltersFactory $filtersFactory)
    {
        $this->filtersFactory = $filtersFactory;
    }

    public function handle(GetTariffsTableItemsQuery $query): TableItems
    {
        $builder = Tariff::query()
            ->with(['town', 'pv', 'role']);

        $filtersPipe = $this->filtersFactory->createFiltersPipe($query->getFilters()->toArray());
        $builder = $filtersPipe->run($builder);

        if ($query->getSortBy() && $query->getSortOrder()) {
            $builder->orderBy($query->getSortBy(), $query->getSortOrder());
        }

        $paginator = $builder->paginate($query->getPerPage(), ['*'], 'page', $query->getPage());

        $items = $paginator
            ->getCollection()
            ->map(function (Tariff $tariff) {
                $updatedAt = null;
                if ($tariff->updated_at) {
                    $updatedAt = $tariff->updated_at->format(DateFormats::USER_SHOW_DATETIME);
                }

                return [
                    'id' => $tariff->id,
                    'name' => $tariff->name,
                    'town' => $tariff->town->only('id', 'name'),
                    'point' => optional($tariff->pv)->only('id', 'name'),
                    'role' => $tariff->role->only('id', 'guard_name'),
                    'price_cfg' => $tariff->price_cfg,
                    'updated_at' => $updatedAt,
                    'date_from' => $tariff->date_from->format(DateFormats::USER_SHOW_DATE),
                    'date_to' => $tariff->date_to->format(DateFormats::USER_SHOW_DATE),
                ];
            })
            ->toArray();

        return new TableItems(
            $items,
            $paginator->total()
        );
    }
}
