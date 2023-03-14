<?php

use Illuminate\Database\Seeder;

class WorkReportsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\WorkReport::class, 1000)->create();
    }
}
