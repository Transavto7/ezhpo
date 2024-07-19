<?php
declare(strict_types=1);

namespace App\Actions\Element\Export;

use DomainException;

interface ExportElementHandler
{
    /**
     * @return string
     * @throws DomainException
     */
    public function handle(): string;

    public static function create(ExportElementAction $action): self;
}
