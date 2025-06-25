<?php

declare(strict_types=1);

namespace Src\Signatures\Eloquent;

use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

/**
 * @property string id
 * @property string status
 * @property int form_id
 * @property string document_type
 * @property string path
 * @property string document_hash
 * @property string created_at
 * @property string updated_at
 */
final class Signature extends Model
{
    use HasTimestamps;

    protected $table = 'signatures';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->uuid = $model->uuid ?? Uuid::uuid4();
        });
    }
}
