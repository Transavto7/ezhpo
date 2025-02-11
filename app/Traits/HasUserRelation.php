<?php

namespace App\Traits;

use App\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasUserRelation
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id', 'entity_id')
            ->where('entity_type', '=', static::ENTITY_TYPE);
    }
}
