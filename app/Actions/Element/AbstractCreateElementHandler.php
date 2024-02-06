<?php

namespace App\Actions\Element;

use Exception;
use Illuminate\Support\Facades\Storage;

abstract class AbstractCreateElementHandler
{
    protected $model;

    /**
     * @throws Exception
     */
    public function __construct(string $modelType)
    {
        //TODO: проверить, что элемент экстендит модель
        $model = app("App\\$modelType");

        if (!$model) {
            throw new Exception("Элемента CRM '$modelType' - не существует");
        }

        $this->model = $model;
    }

    protected function createElement(array $data)
    {
        foreach ($data['files_from_request'] ?? [] as $key => $file) {
            if (isset($data[$key]) && !isset($data[$key . '_base64'])) {
                $data[$key] = Storage::disk('public')->putFile('elements', $file);
            }
        }
        unset($data['files_from_request']);

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = join(',', $value);

                continue;
            }

            if (preg_match('/^data:image\/(\w+);base64,/', $value)) {
                $key = str_replace('_base64', '', $key);

                $base64_image = base64_decode(substr($value, strpos($value, ',') + 1));

                $hash = sha1(time());
                $path = "croppie/$hash.png";
                Storage::disk('public')->put($path, $base64_image);

                $data[$key] = $path;

                continue;
            }

            //TODO: может прийти логическое значение
            $data[$key] = $value
                ? trim($value)
                : $value;
        }

        return $this->model::create($data);
    }

    /**
     * @throws Exception
     */
    protected function generateHashId(
        callable $validator,
        int $min = 0,
        int $max = 999999,
        int $maxTries = 2
    ): int
    {
        $tries = 0;

        do {
            $value = mt_rand($min, $max);

            if ($validator($value)) {
                return $value;
            }

            $tries++;

            if ($tries > $maxTries) {
                throw new Exception('Превышен лимит попыток генерации HASH_ID');
            }
        } while (true);
    }
}
