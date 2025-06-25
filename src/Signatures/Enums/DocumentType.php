<?php

declare(strict_types=1);

namespace Src\Signatures\Enums;

use MyCLabs\Enum\Enum;

/**
 * @method static DocumentType CLOSING()
 * @method static DocumentType PROTOKOL()
 */
final class DocumentType extends Enum
{
    private const CLOSING = 'closing';
    private const PROTOKOL = 'protokol';
}
