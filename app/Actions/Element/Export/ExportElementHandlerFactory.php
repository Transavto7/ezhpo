<?php
declare(strict_types=1);

namespace App\Actions\Element\Export;

use App\Actions\Element\Export\Cars\ExportCarsHandler;
use App\Actions\Element\Export\Drivers\ExportDriversHandler;
use App\Enums\ElementType;

final class ExportElementHandlerFactory
{
    /** @var array<class-string<ExportElementHandler>>  */
    private const HANDLERS = [
        ElementType::DRIVER => ExportDriversHandler::class,
        ElementType::CAR => ExportCarsHandler::class,
    ];

    public static function make(ElementType $type, ExportElementAction $action): ExportElementHandler
    {
        if (! isset(self::HANDLERS[$type->value()])) {
            throw new \DomainException('Unsupported export type: ' . $type->value());
        }

        return self::HANDLERS[$type->value()]::create($action);
    }
}
