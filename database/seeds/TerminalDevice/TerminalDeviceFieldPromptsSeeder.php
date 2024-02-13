<?php

use Illuminate\Database\Seeder;

final class TerminalDeviceFieldPromptsSeeder extends Seeder
{
    private $data = [
        [
            'field' => 'serial_number',
            'name' => 'Серийный номер'
        ],
        [
            'field' => 'date_check',
            'name' => 'Дата поверки'
        ],
        [
            'field' => 'devices',
            'name' => 'Комплектующие'
        ],
    ];
    public function run()
    {
        foreach ($this->data as $item) {
            DB::table('field_prompts')->updateOrInsert([
                'type' => 'terminals',
                'field' => $item['field'],
                'name' => $item['name'],
                'content' => ''
            ]);
        }
    }
}
