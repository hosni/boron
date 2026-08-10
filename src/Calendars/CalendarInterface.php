<?php

declare(strict_types=1);

namespace Boron\Calendars;

/**
 * A calendar system.
 *
 * All conversions between calendars go through the Julian Day Number (JDN):
 * the number of days elapsed since the beginning of the Julian Period.
 * Boron uses the standard "civil" JDN convention (the same one used by ICU),
 * where JDN 2440588 is 1970-01-01 of the proleptic Gregorian calendar.
 */
interface CalendarInterface
{
    /**
     * Canonical name of the calendar (e.g. "jalali", "hijri", "gregorian").
     */
    public function getName(): string;

    /**
     * Convert a date of this calendar to a Julian Day Number.
     */
    public function toJulianDayNumber(int $year, int $month, int $day): int;

    /**
     * Convert a Julian Day Number to a date of this calendar.
     *
     * @return array{0: int, 1: int, 2: int} [year, month, day] (month and day are 1-based)
     */
    public function fromJulianDayNumber(int $julianDayNumber): array;

    public function isLeapYear(int $year): bool;

    public function daysInMonth(int $year, int $month): int;

    public function daysInYear(int $year): int;

    public function monthsInYear(int $year): int;

    /**
     * 1-based day-of-year of the given date.
     */
    public function dayOfYear(int $year, int $month, int $day): int;

    /**
     * @return list<string> month names, index 0 = first month
     */
    public function getMonthNames(string $locale = 'en'): array;

    public function getMonthName(int $month, string $locale = 'en'): string;

    public function isValidDate(int $year, int $month, int $day): bool;
}
