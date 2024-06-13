<?php
declare(strict_types=1);

namespace App\Actions\Element\Export\Cars;

use App\Actions\Element\Export\Core\ElementObject;

final class ExportCar implements ElementObject
{
    /** @var string|null */
    private $companyName;

    /** @var string */
    private $number;

    /** @var string|null */
    private $markModel;

    /** @var string */
    private $category;

    /** @var int */
    private $hashId;

    /**
     * @param string|null $companyName
     * @param string $number
     * @param string|null $markModel
     * @param string $category
     * @param int $hashId
     */
    public function __construct(
        ?string $companyName,
        string  $number,
        ?string $markModel,
        string  $category,
        int     $hashId
    )
    {
        $this->companyName = $companyName;
        $this->number = $number;
        $this->markModel = $markModel;
        $this->category = $category;
        $this->hashId = $hashId;
    }

    public function toArray(): array
    {
        return [
            'company_name' => $this->companyName,
            'gos_number' => $this->number,
            'mark_model' => $this->markModel,
            'type_auto' => $this->category,
            'hash_id' => $this->hashId,
        ];
    }
}
