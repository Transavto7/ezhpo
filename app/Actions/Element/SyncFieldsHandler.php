<?php

namespace App\Actions\Element;

use App\Enums\LogActionTypesEnum;
use App\Log;
use Illuminate\Support\Facades\Auth;

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

        $newValue = $data['fieldSyncValue'];
        $field = $data['fieldSync'];
        //TODO: здесь некорректно брать пользователя, переместить выше
        $userId = Auth::id();

        $query
            ->select([
                'id',
                $field
            ])
            ->get()
            ->each(function ($model) use ($newValue, $field, $userId) {
                $oldValue = $model[$field];

                if ($oldValue == $newValue) return;

                /** @var Log $log */
                $log = Log::create([
                    'user_id' => $userId,
                    'type' => LogActionTypesEnum::UPDATING
                ]);

                $log->setAttribute('data', [
                    [
                        'name' => $field,
                        'oldValue' => $oldValue,
                        'newValue' => $newValue
                    ]
                ]);

                $log->model()->associate($model);

                $log->save();
            });

        return $query->update([$field => $newValue]);
    }
}
