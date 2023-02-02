<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use PDOException;

class IdToHashIdInAnketasCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'anketas:rehash';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change company id from table to hash id of company';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            DB::beginTransaction();
            DB::statement("update `anketas`
                                 left join `companies` on `anketas`.`company_name` = `companies`.`name`
                                 where `anketas`.`company_id` <> `companies`.`hash_id`
                                 set `company_id` = `companies`.`hash_id`");
        } catch (PDOException $ex) {
           $this->error($ex->getMessage());
           DB::rollBack();
           return;
        } finally {
            DB::commit();
            $this->info("ID of companies changed to hash id of its in all anketas.");
        }

    }
}
