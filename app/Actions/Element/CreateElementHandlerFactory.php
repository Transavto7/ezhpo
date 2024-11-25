<?php

namespace App\Actions\Element;

class CreateElementHandlerFactory
{
    /**
     * @throws \Exception
     */
    public function make(string $type): CreateElementHandlerInterface
    {
        switch ($type) {
            case 'Company':
                return new CreateCompanyHandler();
            case 'Car':
                return new CreateCarHandler();
            case 'Driver':
                return new CreateDriverHandler();
            case 'Product':
                return new CreateProductHandler();
            default:
                return new CreateDefaultHandler($type);
        }
    }
}
