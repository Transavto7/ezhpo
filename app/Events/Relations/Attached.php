<?php

namespace App\Events\Relations;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Queue\SerializesModels;

class Attached
{
    use SerializesModels;

    protected $parent;
    protected $related;
    protected $relatedType;

    public function __construct(Model $parent, array $related, string $relatedType)
    {
        $this->parent    = $parent;
        $this->related   = $related;
        $this->relatedType = $relatedType;
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
}
