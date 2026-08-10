<?php

declare(strict_types=1);

namespace Boron\Exceptions;

use RangeException;

class UnsupportedCalendarRangeException extends RangeException implements BoronException
{
    public static function beforeEpoch(string $calendar): self
    {
        return new self(sprintf(
            'The given instant falls before year 1 of the "%s" calendar, which is not supported.',
            $calendar,
        ));
    }
}
