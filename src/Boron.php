<?php

declare(strict_types=1);

namespace Boron;

use Boron\Traits\Date;
use DateTime;

/**
 * Boron (B, atomic number 5) — Carbon (C, atomic number 6) with a
 * multi-calendar system: Jalali (Shamsi), Hijri and Gregorian out of the
 * box, convertible to each other, with both pure-PHP arithmetic drivers
 * (ported from Shahab Yazdi's date-object) and ICU drivers via ext-intl.
 *
 * Boron is built exactly the way Carbon\Carbon itself is built — it
 * extends DateTime directly (NOT Carbon) and gets the entire Carbon
 * feature set from Carbon's Date trait, composed with the multi-calendar
 * layer inside {@see Date}. It is therefore a full
 * CarbonInterface implementation without being a Carbon subclass.
 *
 * When a Carbon *subclass* is required (e.g. Laravel's Date::use()), use
 * the drop-in {@see Carbon} instead.
 */
class Boron extends DateTime implements BoronInterface
{
    use Date;

    /**
     * Returns true if the current class/instance is mutable.
     */
    public static function isMutable(): bool
    {
        return true;
    }
}
