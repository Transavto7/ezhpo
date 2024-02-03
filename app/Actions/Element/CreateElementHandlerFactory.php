<?php

namespace App\Actions\Element;

class CreateElementHandlerFactory
{
    public function make(string $type): CreateElementHandlerInterface
    {
        switch ($type) {
            case 'Company':
                return new CreateCompanyHandler($type);
            case 'Car':
                return new CreateCarHandler($type);
            case 'Driver':
                return new CreateDriverHandler($type);
            case 'Product':
                return new CreateProductHandler($type);
            default:
                return new CreateDefaultHandler($type);
        }
    }
}
