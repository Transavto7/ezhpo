<?php

namespace Src\Core;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class EntityMap
{
    private $table;
    private $ids = [];
    private $idField = 'id';
    private $nameField = 'name';

    protected $query;

    public function __construct(string $table)
    {
        $this->table = $table;
    }

    public function getQuery(): Builder
    {
        if (empty($this->query)) {
            $this->query = DB::table($this->table)
                ->select([$this->idField, DB::raw("{$this->nameField} as name")])
                ->whereIn($this->idField, $this->ids);
        }

        return $this->query;
    }

    public function getKeyValueMap(): array
    {
        if (empty($this->ids)) {
            return [];
        }

        return $this->getQuery()->pluck('name', $this->idField)->toArray();
    }

    public function setIds(array $ids): EntityMap
    {
        $this->ids = array_unique($ids);
        return $this;
    }

    public function addId(string $id): EntityMap
    {
        if (in_array($id, $this->ids, true)) {
            return $this;
        }
        $this->ids[] = $id;

        return $this;
    }

    public function setIdField(string $idField): EntityMap
    {
        $this->idField = $idField;
        return $this;
    }

    public function setNameField(string $nameField): EntityMap
    {
        $this->nameField = $nameField;
        return $this;
    }
}
