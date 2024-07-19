<?php
declare(strict_types=1);

namespace App\Actions\Element\Export\Drivers;

use App\Actions\Element\Export\Core\ElementObject;

final class ExportDriver implements ElementObject
{
    /** @var string|null */
    private $companyName;

    /** @var string */
    private $fullName;

    /** @var int */
    private $userId;

    /**
     * @param string|null $companyName
     * @param string $fullName
     * @param int $userId
     */
    public function __construct(?string $companyName, string $fullName, int $userId)
    {
        $this->companyName = $companyName;
        $this->fullName = $fullName;
        $this->userId = $userId;
    }

    public function toArray(): array
    {
        return [
            'company_name' => $this->companyName,
            'full_name' => $this->fullName,
            'user_id' => $this->userId,
        ];
    }
}
