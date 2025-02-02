<?php

namespace App\Traits;

use App\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasUserRelationTrait
{
    // todo: пока не понял как связь должна быть реализована
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id', 'entity_id')
            ->where('entity_type', '=', static::ENTITY_TYPE);
    }
}
