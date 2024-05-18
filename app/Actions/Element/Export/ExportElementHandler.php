<?php
declare(strict_types=1);

namespace App\Actions\Element\Export;

interface ExportElementHandler
{
    /**
     * @return string
     */
    public function handle(): string;

    public static function create(): self;
}
