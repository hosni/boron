<?php

declare(strict_types=1);

namespace Boron\Exceptions;

use InvalidArgumentException;

class InvalidCalendarDateException extends InvalidArgumentException implements BoronException
{
    public static function forDate(string $calendar, int $year, int $month, int $day): self
    {
        return new self(sprintf(
            '%04d-%02d-%02d is not a valid date in the "%s" calendar.',
            $year,
            $month,
            $day,
            $calendar,
        ));
    }
}
