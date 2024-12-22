<?php

namespace App\Services\SyncFieldPrompts;

use DateTimeImmutable;

class SyncFieldPromptsService
{
    /**
     * @var FieldPromptsRepository
     */
    private $repository;

    /**
     * @var FieldPromptsNormalizer
     */
    private $normalizer;

    /**
     * @param FieldPromptsRepository $repository
     * @param FieldPromptsNormalizer $normalizer
     */
    public function __construct(FieldPromptsRepository $repository, FieldPromptsNormalizer $normalizer)
    {
        $this->repository = $repository;
        $this->normalizer = $normalizer;
    }

    /**
     * @param DateTimeImmutable $date
     * @return array
     */
    public function exportBeforeDate(DateTimeImmutable $date): array
    {
        $fieldPrompts = $this->repository->findAllBeforeDate($date);

        return $this->normalizer->denormalize($fieldPrompts);
    }

    public function import(array $items)
    {
        $fieldPrompts = $this->normalizer->normalize($items);

        $this->repository->delete();
        $this->repository->addItems($fieldPrompts);
    }
}
