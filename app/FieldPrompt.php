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

    public static function moveAfterOther(string $type, string $field, string $previousField)
    {
        $sort = 0;
        $resultSort = 0;

        FieldPrompt::query()
            ->where('type', $type)
            ->where('field', '!=', $field)
            ->orderBy('sort')
            ->orderBy('id')
            ->get()
            ->each(function (FieldPrompt $fieldPrompt) use ($previousField, &$sort, &$resultSort)  {
                $fieldPrompt->update(['sort' => $sort]);
                $sort++;

                if ($fieldPrompt->field === $previousField) {
                    $resultSort = $sort;
                    $sort++;
                }
            });

        if ($resultSort === 0) {
            return;
        }

        FieldPrompt::query()
            ->where('type', $type)
            ->where('field', $field)
            ->update(['sort' => $resultSort]);
    }
}
