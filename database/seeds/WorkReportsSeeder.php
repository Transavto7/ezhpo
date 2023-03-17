<?php

use Illuminate\Database\Seeder;

class WorkReportsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        \App\WorkReport::truncate();

        $users = \App\User::query()
            ->inRandomOrder()
            ->limit(10)
            ->get();

        $pvs = \App\Point::query()
            ->limit(5)
            ->inRandomOrder()
            ->get();


        foreach ($users as $k => $user) {
            $pv = $pvs->random();
            $dateStart = \Carbon\Carbon::create()
                ->year(now()->year)
                ->month(now()->month)
                ->day(now()->day)
                ->hour(6)
                ->minute(random_int(0, 59))
                ->subMonth()
            ;

            factory(\App\WorkReport::class, 10)->make()->each(function (\App\WorkReport $workReport) use ($user, $pv, $dateStart) {
                $dateStart->addDays(2);
                $dateEnd = clone $dateStart;
                $dateEnd->addHours(random_int(4,8))
                    ->minute(random_int(0, 59));

                $workReport->user_id = $user->id;
                $workReport->pv_id = $pv->id;
                $workReport->datetime_begin = $dateStart;
                $workReport->datetime_end = $dateEnd;
                $workReport->save();
            });
        }
    }
}
