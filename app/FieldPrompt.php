<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

/**
 * App\FieldPrompt
 *
 * @property int $id
 * @property string $type
 * @property string $field
 * @property string $name
 * @property string|null $content
 * @property string|null $deleted_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\User|null $deleted_user
 * @method static \Illuminate\Database\Eloquent\Builder|FieldPrompt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FieldPrompt newQuery()
 * @method static Builder|FieldPrompt onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|FieldPrompt query()
 * @method static \Illuminate\Database\Eloquent\Builder|FieldPrompt whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FieldPrompt whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FieldPrompt whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FieldPrompt whereDeletedId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FieldPrompt whereField($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FieldPrompt whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FieldPrompt whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FieldPrompt whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|FieldPrompt whereUpdatedAt($value)
 * @method static Builder|FieldPrompt withTrashed()
 * @method static Builder|FieldPrompt withoutTrashed()
 * @mixin \Eloquent
 */
class FieldPrompt extends Model
{
    use SoftDeletes;

    public $fillable = [
        'type',
        'field',
        'name',
        'content',
        'deleted_id',
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
        $this->deleted_id = user()->id;
        $this->save();

        return parent::delete();
    }
}
