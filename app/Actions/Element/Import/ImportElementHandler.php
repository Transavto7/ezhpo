<?php
declare(strict_types=1);

namespace App\Actions\Element\Import;

interface ImportElementHandler
{
    /**
     * @param ImportElementAction $action
     * @return void
     */
    public function handle(ImportElementAction $action): ImportElementResponse;

    /**
     * @return self
     */
    public static function create(): self;
}
