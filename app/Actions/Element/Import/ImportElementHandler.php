<?php
declare(strict_types=1);

namespace App\Actions\Element\Import;

use App\Actions\Element\Import\Exceptions\FoundedNotValidElements;

interface ImportElementHandler
{
    /**
     * @param ImportElementAction $action
     * @return void
     * @throws FoundedNotValidElements
     */
    public function handle(ImportElementAction $action): void;

    /**
     * @return self
     */
    public static function create(): self;
}
