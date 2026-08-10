<?php

declare(strict_types=1);

namespace Boron;

use Boron\Calendars\CalendarInterface;
use Carbon\CarbonInterface as BaseCarbonInterface;
use DateTimeZone;

/**
 * Boron's contract: everything Carbon can do ({@see BaseCarbonInterface}),
 * plus the multi-calendar API.
 *
 * Implemented by {@see Carbon} and {@see CarbonImmutable}. Prefer this
 * type-hint when you need calendar methods; prefer {@see BaseCarbonInterface}
 * when any Carbon-compatible object is enough.
 *
 * toMutable() / toImmutable() are narrowed so conversions stay inside the
 * Boron family and never leak plain Carbon instances.
 *
 * Calendar-aware magic properties (active calendar; Carbon getters stay Gregorian):
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
 */
interface CarbonInterface extends BaseCarbonInterface
{
    // /////////////////////////////////////////////////////////////////
    // //////////////////////// CONFIGURATION //////////////////////////
    // /////////////////////////////////////////////////////////////////

    /**
     * Set the process-wide default calendar (shared by every Boron class).
     */
    public static function setDefaultCalendar(string|CalendarInterface $calendar): void;

    public static function getDefaultCalendar(): CalendarInterface;

    /**
     * Default locale used for calendar month names ("en", "fa", "ar", ...).
     */
    public static function setDefaultCalendarLocale(string $locale): void;

    public static function getDefaultCalendarLocale(): string;

    // /////////////////////////////////////////////////////////////////
    // ///////////////////////// CREATION ///////////////////////////////
    // /////////////////////////////////////////////////////////////////

    public static function fromCalendar(
        string|CalendarInterface $calendar,
        int $year,
        int $month = 1,
        int $day = 1,
        int $hour = 0,
        int $minute = 0,
        int $second = 0,
        DateTimeZone|string|null $timezone = null,
    ): static;

    public static function fromJalali(
        int $year,
        int $month = 1,
        int $day = 1,
        int $hour = 0,
        int $minute = 0,
        int $second = 0,
        DateTimeZone|string|null $timezone = null,
    ): static;

    public static function fromHijri(
        int $year,
        int $month = 1,
        int $day = 1,
        int $hour = 0,
        int $minute = 0,
        int $second = 0,
        DateTimeZone|string|null $timezone = null,
    ): static;

    public static function parseFromCalendar(
        string|CalendarInterface $calendar,
        string $value,
        DateTimeZone|string|null $timezone = null,
    ): static;

    // /////////////////////////////////////////////////////////////////
    // ////////////////////// ACTIVE CALENDAR //////////////////////////
    // /////////////////////////////////////////////////////////////////

    /**
     * Return an instance whose active calendar is the given one (null to
     * follow the global default again).
     */
    public function withCalendar(string|CalendarInterface|null $calendar): static;

    public function getCalendar(): CalendarInterface;

    /**
     * Raw active calendar, null when the instance follows the global default.
     */
    public function getActiveCalendar(): CalendarInterface|string|null;

    // /////////////////////////////////////////////////////////////////
    // ///////////////////////// CONVERSION /////////////////////////////
    // /////////////////////////////////////////////////////////////////

    /**
     * Civil Julian Day Number of the current date (in its timezone).
     */
    public function julianDayNumber(): int;

    public function toCalendarDate(string|CalendarInterface|null $calendar = null): CalendarDate;

    public function toJalali(): CalendarDate;

    public function toHijri(): CalendarDate;

    public function toGregorianDate(): CalendarDate;

    // /////////////////////////////////////////////////////////////////
    // ///////////////////////// MODIFIERS //////////////////////////////
    // /////////////////////////////////////////////////////////////////

    public function setCalendarDate(
        int $year,
        int $month,
        int $day,
        string|CalendarInterface|null $calendar = null,
    ): static;

    public function addCalendarMonths(int $months = 1): static;

    public function subCalendarMonths(int $months = 1): static;

    public function addCalendarMonth(): static;

    public function subCalendarMonth(): static;

    public function addCalendarYears(int $years = 1): static;

    public function subCalendarYears(int $years = 1): static;

    public function addCalendarYear(): static;

    public function subCalendarYear(): static;

    public function startOfCalendarMonth(): static;

    public function endOfCalendarMonth(): static;

    public function startOfCalendarYear(): static;

    public function endOfCalendarYear(): static;

    // /////////////////////////////////////////////////////////////////
    // ///////////////////////// INSPECTION /////////////////////////////
    // /////////////////////////////////////////////////////////////////

    public function isCalendarLeapYear(string|CalendarInterface|null $calendar = null): bool;

    public function calendarDaysInMonth(string|CalendarInterface|null $calendar = null): int;

    // /////////////////////////////////////////////////////////////////
    // ///////////////////////// FORMATTING /////////////////////////////
    // /////////////////////////////////////////////////////////////////

    public function calendarFormat(
        string $format,
        string|CalendarInterface|null $calendar = null,
        ?string $locale = null,
        bool $localizeDigits = false,
    ): string;

    public function toCalendarDateString(string|CalendarInterface|null $calendar = null): string;

    public function toCalendarDateTimeString(string|CalendarInterface|null $calendar = null): string;

    // /////////////////////////////////////////////////////////////////
    // ///////////////////////// MUTABILITY /////////////////////////////
    // /////////////////////////////////////////////////////////////////

    /**
     * Return a mutable Boron Carbon copy (never a plain Carbon), keeping
     * the active calendar.
     */
    public function toMutable(): Carbon;

    /**
     * Return an immutable Boron CarbonImmutable copy (never a plain
     * CarbonImmutable), keeping the active calendar.
     */
    public function toImmutable(): CarbonImmutable;
}
