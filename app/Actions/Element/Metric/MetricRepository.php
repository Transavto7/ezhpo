<?php

namespace App\Actions\Element\Metric;

use Illuminate\Support\Facades\DB;

final class MetricRepository
{
    /**
     * @var GenerateMetricAction
     */
    protected $action;

    /**
     * @param GenerateMetricAction $action
     */
    public function __construct(GenerateMetricAction $action)
    {
        $this->action = $action;
    }

    public function get(): array
    {
        $start = $this->action->getStartDate();
        $end = $this->action->getEndDate()->clone()->addDay();

        $actions = DB::table('user_actions')
            ->select([
                'companies.name',
                'user_actions.type',
                DB::raw('count(*) as count'),
            ])
            ->join(
                'users',
                'user_actions.user_id',
                '=',
                'users.id'
            )
            ->leftJoin(
                'companies',
                'users.company_id',
                '=',
                'companies.id'
            )
            ->where('user_actions.created_at', '>=', $start)
            ->where('user_actions.created_at', '<=', $end)
            ->groupBy(['companies.name', 'user_actions.type'])
            ->orderBy('companies.name')
            ->get()
            ->toArray();

        $companyActions = array_reduce($actions, function ($carry, $item) {
            $name = $item->name;

            if ($name === null) {
                $name = 'emptyName';
            }

            $carry[$name][$item->type] = $item->count;

            return $carry;
        }, []);

        return array_map(function ($name, $actions) {
            return new Metric(
                $name,
                (new ActionTypeGroup())->fromType($actions)
            );
        }, array_keys($companyActions), $companyActions);
    }
}
