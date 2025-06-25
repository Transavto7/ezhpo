<?php

declare(strict_types=1);

namespace Src\Signatures\Enums;

use MyCLabs\Enum\Enum;

/**
 * @method static SignatureStatus NEED_SIGN()
 * @method static SignatureStatus SIGNED()
 * @method static SignatureStatus INVALIDATED()
 */
final class SignatureStatus extends Enum
{
    const NEED_SIGN = 'need_signed';

    const SIGNED = 'signed';

    const INVALIDATED = 'invalidated';
}
