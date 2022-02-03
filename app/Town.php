<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Town extends Model
{
    public $fillable = ['id', 'hash_id', 'name'];

    public static function getName ($id)
    {
        $id = explode(',', $id);

        $data = self::whereIn('id', $id)->get();

        if(!$data) {
            $data = '';
        } else {
            $newData = '';

            foreach($data as $dataItemKey => $dataItem) {
                $newData .= ($dataItemKey !== 0 ? ', ' : '') . $dataItem->name;
            }

            $data = $newData;
        }

        return $data;
    }

    public static function getAll () {
        return self::all();
    }
}
