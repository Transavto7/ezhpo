<?php

namespace App\Console\Commands\Setting;

use App\Settings;
use Illuminate\Console\Command;

class AddSetting extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setting:add {--key=} {--value=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $key = $this->option('key');
        if (!$key) {
            $this->error('Enter key setting!');
            return;
        }

        $value = $this->option('value') ?? null;

        Settings::create([
            'key' => $key,
            'value' => $value
        ]);
        $this->info("Add setting '$key' to value '$value'");
    }
}
