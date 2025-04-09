<?php

namespace App\Services\FindSimilarElement;

use App\Enums\ElementType;
use App\Services\FindSimilarElement\FindSimilarCar\FindSimilarCarHandler;
use App\Services\FindSimilarElement\FindSimilarCompany\FindSimilarCompanyHandler;
use App\Services\FindSimilarElement\FindSimilarDriver\FindSimilarDriverHandler;

final class FindSimilarElementHandlerFactory
{
    public function make(ElementType $type)
    {
        switch (true) {
            case $type->value() === ElementType::DRIVER:
                return app(FindSimilarDriverHandler::class);
            case $type->value() === ElementType::CAR:
                return app(FindSimilarCarHandler::class);
            case $type->value() === ElementType::COMPANY:
                return app(FindSimilarCompanyHandler::class);
            default:
                throw new \Exception('Undefined element type '.$type->value());
        }
    }
}
