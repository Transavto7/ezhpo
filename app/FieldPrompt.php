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
        'sort',
    ];

    public static function getTypes(): array
    {
        $types = [];
        foreach (self::groupBy('type')->pluck('type') as $type) {
            $types[] = [
                'key' => $type,
                'name' => __('ankets.'.strtolower($type)),
            ];
        }

        return $types;
    }

    public static function getFields(): array
    {
        $fields = [];
        foreach (self::query()->select('type', 'field', 'name')->get() as $field) {
            $fields[$field->type][] = [
                'key' => $field->field,
                'name' => $field->name,
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

    public static function moveBeforeOther(string $type, string $field, string $nextField)
    {
        $sort = 0;
        $resultSort = null;

        self::query()
            ->where('type', $type)
            ->where('field', '!=', $field)
            ->orderBy('sort')
            ->orderBy('id')
            ->get()
            ->each(function (self $fieldPrompt) use ($nextField, &$sort, &$resultSort) {
                if ($fieldPrompt->field === $nextField) {
                    $resultSort = $sort;
                    $sort++;
                }

                $fieldPrompt->update(['sort' => $sort]);
                $sort++;
            });

        if ($resultSort === null) {
            return;
        }

        self::query()
            ->where('type', $type)
            ->where('field', $field)
            ->update(['sort' => $resultSort]);
    }

    public static function moveAfterOther(string $type, string $field, string $previousField)
    {
        $sort = 0;
        $resultSort = 0;

        self::query()
            ->where('type', $type)
            ->where('field', '!=', $field)
            ->orderBy('sort')
            ->orderBy('id')
            ->get()
            ->each(function (self $fieldPrompt) use ($previousField, &$sort, &$resultSort) {
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

        self::query()
            ->where('type', $type)
            ->where('field', $field)
            ->update(['sort' => $resultSort]);
    }
}
