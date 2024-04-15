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
        'name',
        'content',
        'deleted_id',
        'sort'
    ];

    public static function getTypes(): array
    {
        $types = [];
        foreach (FieldPrompt::groupBy('type')->pluck('type') as $type) {
            $types[] = [
                'key' => $type,
                'name' => __('ankets.' . strtolower($type)),
            ];
        }

        return $types;
    }

    public static function getFields(): array
    {
        $fields = [];
        foreach (FieldPrompt::select('type', 'field', 'name')->get() as $field) {
            $fields[$field->type][] = [
                'key' => $field->field,
                'name'=> $field->name
            ];
        }

        return $fields;
    }

    public function deleted_user()
    {
        return $this->belongsTo(User::class, 'deleted_id', 'id')
            ->withDefault();
    }

    public function delete()
    {
        $user = user();
        if ($user) {
            $this->deleted_id = $user->id;
            $this->save();
        }

        return parent::delete();
    }
}
