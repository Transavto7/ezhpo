<?php
declare(strict_types=1);

namespace App\Actions\Element\Import\Core;

abstract class ElementHydrator
{
    /** @var array */
    protected $attributesMap = [];

    /**
     * @param  array  $record
     * @return array
     */
    abstract function associate(array $record): array;

    /**
     * @param array $row
     * @return ErrorObject
     */
    abstract public function hydrate(array $row);
}
