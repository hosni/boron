<?php

declare(strict_types=1);

namespace Boron\Exceptions;

use InvalidArgumentException;

class InvalidFormatException extends InvalidArgumentException implements BoronException
{
    public static function forValue(string $value, string $calendar): self
    {
        return new self(sprintf(
            'Unable to parse "%s" as a date of the "%s" calendar.',
            $value,
            $calendar,
        ));
    }
}
