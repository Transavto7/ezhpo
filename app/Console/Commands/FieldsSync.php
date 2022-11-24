<?php

namespace App\Console\Commands;

use App\FieldPrompt;
use Illuminate\Console\Command;

class FieldsSync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fields:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize field list';

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
        $fields = FieldPrompt::withoutTrashed()->get();
        FieldPrompt::withoutTrashed()->forceDelete();

        foreach (config('fields.registries') as $registryName => $registryFields) {
            foreach ($registryFields as $fieldKey => $fieldName) {
                $oldFiled = $fields->where('type', $registryName)->where('field', $fieldKey)->first();
                $name = $oldFiled ? $oldFiled->name : $fieldName;
                $content = $oldFiled ? $oldFiled->content : '';
                $deleted_at = $oldFiled ? $oldFiled->deleted_at : null;

                FieldPrompt::create([
                    'name' => $name,
                    'type' => $registryName,
                    'field' => $fieldKey,
                    'content' => $content,
                    $deleted_at => $deleted_at
                ]);
            }
        }

        $this->info('Sync all fields ' . $fields->count() . ' => ' . FieldPrompt::count());
    }
}
