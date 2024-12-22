<?php

namespace App\Services\SyncFieldPrompts;

use DateTimeImmutable;

class FieldPromptsNormalizer
{
    /**
     * @param array $items
     * @return FieldPrompt[]
     */
    public function normalize(array $items): array
    {
        return array_map(function ($item) {
            $deletedAt = null;
            if ($item['deleted_at']) {
                $deletedAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $item['deleted_at']);
            }

            $createdAt = null;
            if ($item['created_at']) {
                $createdAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $item['created_at']);
            }

            $updatedAt = null;
            if ($item['updated_at']) {
                $updatedAt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $item['updated_at']);
            }

            return new FieldPrompt(
                $item['id'],
                $item['type'],
                $item['field'],
                $item['name'],
                $item['content'],
                $item['deleted_id'],
                $deletedAt,
                $createdAt,
                $updatedAt,
                $item['sort']
            );
        }, $items);
    }

    /**
     * @param FieldPrompt[] $items
     * @return array
     */
    public function denormalize(array $items): array
    {
        return array_map(function (FieldPrompt $item) {
            $deletedAt = null;
            if ($item->getDeletedAt()) {
                $deletedAt = $item->getDeletedAt()->format('Y-m-d H:i:s');
            }

            $createdAt = null;
            if ($item->getCreatedAt()) {
                $createdAt = $item->getCreatedAt()->format('Y-m-d H:i:s');
            }

            $updatedAt = null;
            if ($item->getUpdatedAt()) {
                $updatedAt = $item->getUpdatedAt()->format('Y-m-d H:i:s');
            }

            return [
                'id' => $item->getId(),
                'type' => $item->getType(),
                'field' => $item->getField(),
                'name' => $item->getName(),
                'content' => $item->getContent(),
                'deleted_id' => $item->getDeletedId(),
                'deleted_at' => $deletedAt,
                'created_at' => $createdAt,
                'updated_at' => $updatedAt,
                'sort' => $item->getSort(),
            ];
        }, $items);
    }
}
