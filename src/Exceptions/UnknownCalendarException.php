<?php

declare(strict_types=1);

namespace Boron\Exceptions;

use InvalidArgumentException;

class UnknownCalendarException extends InvalidArgumentException implements BoronException
{
    /**
     * @param list<string> $known
     */
    public static function forName(string $name, array $known = []): self
    {
        $message = "Unknown calendar \"$name\".";

        if ([] !== $known) {
            $message .= ' Known calendars: '.implode(', ', $known).'.';
        }

        return new self($message);
    }
}
