<?php

declare(strict_types=1);

namespace Boron;

use Boron\Traits\Date;
use DateTimeImmutable;

/**
 * Immutable variant of {@see Boron}: extends DateTimeImmutable directly
 * (NOT CarbonImmutable), built the same way Carbon\CarbonImmutable is.
 */
class BoronImmutable extends DateTimeImmutable implements BoronInterface
{
    use Date;

    /**
     * Returns false as the current class is immutable.
     */
    public static function isMutable(): bool
    {
        return false;
    }
}
