<?php

declare(strict_types=1);

namespace Boron\Exceptions;

use RuntimeException;

class IntlExtensionNotLoadedException extends RuntimeException implements BoronException
{
    public static function forCalendar(string $name): self
    {
        return new self(sprintf(
            'The "%s" calendar requires the PHP intl extension, which is not loaded.',
            $name,
        ));
    }
}
