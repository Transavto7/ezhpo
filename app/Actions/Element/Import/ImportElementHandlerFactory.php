<?php
declare(strict_types=1);

namespace App\Actions\Element\Import;

use App\Actions\Element\Import\Cars\ImportCarHandler;
use App\Actions\Element\Import\Drivers\ImportDriverHandler;
use App\Enums\ElementType;

final class ImportElementHandlerFactory
{
    private const HANDLERS = [
        ElementType::DRIVER => ImportDriverHandler::class,
        ElementType::CAR => ImportCarHandler::class,
    ];

    public static function make(ElementType $type): ImportElementHandler
    {
        if (! isset(self::HANDLERS[$type->value()])) {
            throw new \DomainException('Unsupported import type: ' . $type->value());
        }

        return self::HANDLERS[$type->value()]::create();
    }
}
