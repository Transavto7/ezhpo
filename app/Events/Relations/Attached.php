<?php

namespace App\Events\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;
use Ramsey\Uuid\Uuid;

class Attached
{
    use SerializesModels;

    protected $parent;
    protected $related;
    protected $relatedType;
    protected $uuid;

    public function __construct(Model $parent, array $related, string $relatedType, string $uuid = null)
    {
        $this->parent    = $parent;
        $this->related   = $related;
        $this->relatedType = $relatedType;
        $this->uuid = $uuid ?? Uuid::uuid4();
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getRelated(): array
    {
        return $this->related;
    }

    public function getRelatedType(): string
    {
        return $this->relatedType;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }
}
