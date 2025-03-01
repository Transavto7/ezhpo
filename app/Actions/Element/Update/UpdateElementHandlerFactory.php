<?php

namespace App\Actions\Element\Update;

use Exception;

class UpdateElementHandlerFactory
{
    /**
     * @throws Exception
     */
    public function make(string $type): UpdateElementHandlerInterface
    {
        switch ($type) {
            case 'Car':
                return new UpdateCarHandler($type);
            case 'Driver':
                return new UpdateDriverHandler($type);
            case 'Company':
                return new UpdateCompanyHandler($type);
            case 'Instr':
                return new UpdateInstrHandler($type);
            case 'Product':
                return new UpdateProductHandler($type);
            default:
                return new UpdateElementHandler($type);
        }
    }
}
