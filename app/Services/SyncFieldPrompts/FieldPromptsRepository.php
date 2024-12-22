<?php

namespace App\Services\SyncFieldPrompts;

use DateTimeImmutable;
use Illuminate\Support\Facades\DB;

class FieldPromptsRepository
{
    /**
     * @param DateTimeImmutable $date
     * @return FieldPrompt[]
     */
    public function findAllBeforeDate(DateTimeImmutable $date): array
    {
        $data = DB::table('field_prompts')
            ->select([
                'id',
                'type',
                'field',
                'name',
                'content',
                'deleted_id',
                'deleted_at',
                'created_at',
                'updated_at',
                'sort'
            ])
            ->where('created_at', '<=', $date)
            ->orderBy('created_at')
            ->get()
            ->toArray();

        return array_map(function ($item) {
            $deletedAt = null;
            if ($item->deleted_at) {
                $deletedAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $item->deleted_at);
            }

            $createdAt = null;
            if ($item->created_at) {
                $createdAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $item->created_at);
            }

            $updatedAt = null;
            if ($item->updated_at) {
                $updatedAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $item->updated_at);
            }

            return new FieldPrompt(
                $item->id,
                $item->type,
                $item->field,
                $item->name,
                $item->content,
                $item->deleted_id,
                $deletedAt,
                $createdAt,
                $updatedAt,
                $item->sort
            );
        }, $data);
    }

    public function delete()
    {
        DB::table('field_prompts')->delete();
    }

    /**
     * @param FieldPrompt[] $items
     * @return void
     */
    public function addItems(array $items)
    {
        $payload = [];
        foreach ($items as $item) {
            $payload[] = [
                'type' => $item->getType(),
                'field' => $item->getField(),
                'name' => $item->getName(),
                'content' => $item->getContent(),
                'deleted_id' => $item->getDeletedId(),
                'deleted_at' => $item->getDeletedAt(),
                'created_at' => $item->getCreatedAt(),
                'updated_at' => $item->getUpdatedAt(),
                'sort' => $item->getSort(),
            ];
        }

        DB::table('field_prompts')->insert($payload);
    }
}
