<?php
declare(strict_types=1);

namespace App\Actions\Element\Export\Core;

interface ElementObject
{
    /**
     * @return array
     */
    public function toArray(): array;
}
