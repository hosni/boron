<?php

declare(strict_types=1);

namespace Boron;

use Boron\Concerns\CarbonBridge;
use Carbon\CarbonImmutable as BaseCarbonImmutable;

/**
 * Drop-in replacement for Carbon\CarbonImmutable: a true CarbonImmutable
 * subclass with the Boron multi-calendar system on top.
 *
 * Calendar-aware magic properties (resolved through the active calendar;
 * Carbon getters like {@see $year} stay Gregorian):
 *
 * @property-read int          $calendarYear        Year in the active calendar
 * @property-read int          $calendarMonth       Month in the active calendar (1-based)
 * @property-read int          $calendarDay         Day of month in the active calendar
 * @property-read CalendarDate $calendarDate        Date triple in the active calendar
 * @property-read string       $calendarMonthName   Localized month name (default calendar locale)
 * @property-read int          $calendarDaysInMonth Length of the active calendar month
 * @property-read int          $calendarDayOfYear   1-based day of year in the active calendar
 * @property-read string       $calendarName        Canonical name of the active calendar
 * @property-read int          $julianDay           Civil Julian Day Number of the current date
 *
 * @see Carbon for the mutable drop-in variant.
 */
class CarbonImmutable extends BaseCarbonImmutable implements CarbonInterface
{
    use CarbonBridge;
}
