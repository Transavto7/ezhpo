<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class ElementsService implements Contracts\ServiceInterface
{
    /**
     * @param  int  $min
     * @param  int  $max
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return int
     */
    public static function generateSafeHashId(int $min, int $max, Model $model): int
    {
        $hash_id = mt_rand($min, $max);
        while ($model->newQuery()->where(['hash_id' => $hash_id])->exists()) {
            $hash_id = mt_rand($min, $max);
        }

        return $hash_id;
    }

    /**
     * @param  int  $min
     * @param  int  $max
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return string
     */
    public static function generateSafeEmail(int $min, int $max, Model $model): string
    {
        $email = mt_rand($min, $max).'@ta-7.ru';
        while ($model->newQuery()->where(['email' => $email])->exists()) {
            $email = mt_rand($min, $max).'@ta-7.ru';
        }

        return $email;
    }

}
