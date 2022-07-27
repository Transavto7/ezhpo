<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    public $fillable = ['hash_id', 'name', 'pv_id', 'company_id'];

    // Получение пункта выпуска
    public static function getPointText ($id = 0) {
        $point = Point::find($id);

        if($point) {
            $point = $point->name;
        } else {
            $point = '';
        }

        return $point;
    }

    public function town()
    {
        return $this->belongsTo(Town::class, 'pv_id');
    }

    // Получение всех пунктов
    public static function getAll ($basic = false) {
        $towns = Town::all();

        if(!$basic) {
            $result_points_towns = [];

            // Ищем дочерние элемеенты
            foreach($towns as $town) {
                $data = [
                    'name' => $town->name,
                    'id' => $town->id,
                    'pvs' => Point::where('pv_id', $town->id)->get()
                ];

                array_push($result_points_towns, $data);
            }

            return $result_points_towns;
        } else {
            return self::all();
        }
    }

}
