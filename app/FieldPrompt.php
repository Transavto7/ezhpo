<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FieldPrompt extends Model
{
    use SoftDeletes;

    public $fillable = [
        'type',
        'field',
        'content',
        'deleted_id',
    ];

    /*
     * return array
     * journal => array key and name fields
     */
    public static function getFieldList(): array
    {
        $result = [];

        $result['medic'] = self::getFieldsByType('medic');
        $result['tech'] = self::getFieldsByType('tech');
        $result['bdd'] = self::getFieldsByType('bdd');
        $result['dop'] = self::getFieldsByType('Dop');
        $result['pechat_pl'] = self::getFieldsByType('pechat_pl');
        $result['report_cart'] = self::getFieldsByType('report_cart');

        return $result;
    }

    /*
     * return array key => name
     * by only key array
     */
    private static function getFieldsByType(string $type): array
    {
        $result = [];

        foreach (Anketa::$fieldsKeysTable[$type] as $key) {
            $result[] =[
              'key' => $key,
              'name' => __("fields.$type.$key"),
            ];
        }

        return $result;
    }

    /*
     * Return all types journal in system
     */
    public static function getTypes(): array
    {
        return [
                [
                    'key' => 'medic',
                    'name' => __('ankets.medic'),
                ],
                [
                    'key' => 'tech',
                    'name' => __('ankets.tech'),
                ],
                [
                    'key' => 'bdd',
                    'name' => __('ankets.bdd'),
                ],
                [
                    'key' => 'dop',
                    'name' => __('ankets.dop'),
                ],
                [
                    'key' => 'pechat_pl',
                    'name' => __('ankets.pechat_pl'),
                ],
                [
                    'key' => 'report_cart',
                    'name' => __('ankets.report_cart'),
                ],
        ];
    }

    public function deleted_user()
    {
        return $this->belongsTo(User::class, 'deleted_id', 'id')
            ->withDefault();
    }

    public function delete()
    {
        $this->deleted_id = user()->id;
        $this->save();

        return parent::delete();
    }
}
