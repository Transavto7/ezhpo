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
        $companyActions = DB::select("
                with
                    company_action as (
                        select
                            c.name as name,
                            ua.type as type,
                            count(*) as count
                        from
                            user_actions as ua
                        join
                            users u on u.id = ua.user_id
                        left
                            join companies c on u.company_id = c.id
                        where
                            ua.created_at >= '{$this->action->getStartDate()->format('Y-m-d')}' and
                            ua.created_at <= '{$this->action->getEndDate()->format('Y-m-d')}'
                        group by
                            c.name, ua.type
                        order by
                            c.name
                    )

                select
                    name,
                    json_objectagg(type, count) as actions
                from
                    company_action
                group by
                    name
            ");

        return array_map(function ($company) {
            $actions = (array)json_decode($company->actions, true);

            return new Metric(
                $company->name,
                (new ActionTypeGroup())->fromType($actions)
            );
        }, $companyActions);
    }
}
