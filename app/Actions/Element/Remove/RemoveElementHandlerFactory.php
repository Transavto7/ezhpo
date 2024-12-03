<?php

namespace App\Actions\Element\Remove;

use Exception;

class RemoveElementHandlerFactory
{
    /**
     * @throws Exception
     */
    public function make(string $type): RemoveElementHandlerInterface
    {
        switch ($type) {
            case 'Company':
                return new RemoveCompanyHandler();
            default:
                return new RemoveElementHandler($type);
        }
    }
}
