<?php

namespace App\Actions\Element;

class SyncFieldsHandler
{
    public function handle(array $data): int
    {
        $model = app("App\\$data[model]");

        if (!$model) {
            return 0;
        }

        $query = $model->where($data['fieldFind'], $data['fieldFindId']);

        if ($data['model'] === 'Driver' || $data['model'] === 'Car') {
            $query->where('autosync_fields', 'LIKE', "%$data[fieldSync]%");
        }

        return $query->update([$data['fieldSync'] => $data['fieldSyncValue']]);
    }
}
