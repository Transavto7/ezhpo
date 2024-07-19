<?php
declare(strict_types=1);

namespace App\Actions\Element\Import\Exceptions;

final class FoundedNotValidElements extends \Exception
{
    /** @var string */
    private $fileName;

    /** @var string */
    private $disk;

    public function __construct(string $fileName, string $disk)
    {
        $this->fileName = $fileName;
        $this->disk = $disk;
        parent::__construct('Найдены ошибочные записи в файле!');
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getDisk(): string
    {
        return $this->disk;
    }
}
