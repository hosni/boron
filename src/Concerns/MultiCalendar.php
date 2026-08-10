<?php

declare(strict_types=1);

namespace Boron\Concerns;

use Boron\CalendarDate;
use Boron\CalendarRegistry;
use Boron\Calendars\CalendarInterface;
use Boron\Carbon;
use Boron\CarbonImmutable;
use Boron\Exceptions\InvalidFormatException;
use Boron\Support\Digits;
use Carbon\Unit;
use DateTimeImmutable;
use DateTimeZone;

/**
 * The multi-calendar layer shared by every Boron class.
 *
 * The underlying instant is always a plain (Gregorian) DateTime, so every
 * Carbon feature keeps working unchanged. The active calendar only affects
 * the calendar-aware members (toCalendarDate(), calendarFormat(),
 * addCalendarMonths(), ...).
 *
 * This trait is calendar logic only; the glue that hooks it into Carbon's
 * get()/__serialize()/__unserialize() lives in {@see CarbonBridge}.
 */
trait MultiCalendar
{
    // /////////////////////////////////////////////////////////////////
    // //////////////////////// CONFIGURATION //////////////////////////
    // /////////////////////////////////////////////////////////////////

    /**
     * Set the process-wide default calendar (shared by Carbon and
     * CarbonImmutable).
     */
    public static function setDefaultCalendar(string|CalendarInterface $calendar): void
    {
        CalendarRegistry::setDefaultCalendar($calendar);
    }

    public static function getDefaultCalendar(): CalendarInterface
    {
        return CalendarRegistry::getDefaultCalendar();
    }

    /**
     * Default locale used for calendar month names ("en", "fa", "ar", ...).
     */
    public static function setDefaultCalendarLocale(string $locale): void
    {
        CalendarRegistry::setDefaultLocale($locale);
    }

    public static function getDefaultCalendarLocale(): string
    {
        return CalendarRegistry::getDefaultLocale();
    }

    // /////////////////////////////////////////////////////////////////
    // ///////////////////////// CREATION ///////////////////////////////
    // /////////////////////////////////////////////////////////////////

    /**
     * Create an instance from a date expressed in any calendar.
     */
    public static function fromCalendar(
        string|CalendarInterface $calendar,
        int $year,
        int $month = 1,
        int $day = 1,
        int $hour = 0,
        int $minute = 0,
        int $second = 0,
        DateTimeZone|string|null $timezone = null,
    ): static {
        $calendar = CalendarRegistry::resolve($calendar);
        $julianDayNumber = $calendar->toJulianDayNumber($year, $month, $day);

        [$gregorianYear, $gregorianMonth, $gregorianDay] = CalendarRegistry::gregorian()
            ->fromJulianDayNumber($julianDayNumber);

        return static::create($gregorianYear, $gregorianMonth, $gregorianDay, $hour, $minute, $second, $timezone)
            ->withCalendar($calendar);
    }

    public static function fromJalali(
        int $year,
        int $month = 1,
        int $day = 1,
        int $hour = 0,
        int $minute = 0,
        int $second = 0,
        DateTimeZone|string|null $timezone = null,
    ): static {
        return static::fromCalendar('jalali', $year, $month, $day, $hour, $minute, $second, $timezone);
    }

    public static function fromHijri(
        int $year,
        int $month = 1,
        int $day = 1,
        int $hour = 0,
        int $minute = 0,
        int $second = 0,
        DateTimeZone|string|null $timezone = null,
    ): static {
        return static::fromCalendar('hijri', $year, $month, $day, $hour, $minute, $second, $timezone);
    }

    /**
     * Parse a "Y-m-d", "Y/m/d" or "Y.m.d" date string (optionally followed
     * by H:i or H:i:s) expressed in the given calendar. Persian and
     * Arabic-Indic digits are accepted.
     */
    public static function parseFromCalendar(
        string|CalendarInterface $calendar,
        string $value,
        DateTimeZone|string|null $timezone = null,
    ): static {
        $normalized = Digits::toLatin($value);

        $pattern = '/^\s*(\d{1,5})\s*[-\/.]\s*(\d{1,2})\s*[-\/.]\s*(\d{1,2})'
            .'(?:[T\s]+(\d{1,2})\s*:\s*(\d{1,2})(?:\s*:\s*(\d{1,2}))?)?\s*$/u';

        if (!preg_match($pattern, $normalized, $matches)) {
            throw InvalidFormatException::forValue($value, CalendarRegistry::resolve($calendar)->getName());
        }

        return static::fromCalendar(
            $calendar,
            (int) $matches[1],
            (int) $matches[2],
            (int) $matches[3],
            (int) ($matches[4] ?? 0),
            (int) ($matches[5] ?? 0),
            (int) ($matches[6] ?? 0),
            $timezone,
        );
    }
    /**
     * Active calendar of this instance; null means the global default.
     */
    protected CalendarInterface|string|null $activeCalendar = null;

    // /////////////////////////////////////////////////////////////////
    // ////////////////////// ACTIVE CALENDAR //////////////////////////
    // /////////////////////////////////////////////////////////////////

    /**
     * Return an instance whose active calendar is the given one.
     * Mutable instances are modified in place, immutable ones are cloned,
     * mirroring Carbon semantics.
     *
     * Note: this is intentionally NOT called calendar() because Carbon
     * already defines a calendar() method (moment.js-style human display).
     */
    public function withCalendar(string|CalendarInterface|null $calendar): static
    {
        if (\is_string($calendar)) {
            // Resolve eagerly to validate the name and normalize aliases.
            $calendar = CalendarRegistry::get($calendar);
        }

        $instance = $this instanceof DateTimeImmutable ? clone $this : $this;
        $instance->activeCalendar = $calendar;

        return $instance;
    }

    public function getCalendar(): CalendarInterface
    {
        if (null === $this->activeCalendar) {
            return CalendarRegistry::getDefaultCalendar();
        }

        return CalendarRegistry::resolve($this->activeCalendar);
    }

    /**
     * Raw active calendar, null when the instance follows the global
     * default. Mainly used internally to propagate the calendar to copies.
     */
    public function getActiveCalendar(): CalendarInterface|string|null
    {
        return $this->activeCalendar;
    }

    // /////////////////////////////////////////////////////////////////
    // ///////////////////////// CONVERSION /////////////////////////////
    // /////////////////////////////////////////////////////////////////

    /**
     * Civil Julian Day Number of the current date (in its timezone).
     */
    public function julianDayNumber(): int
    {
        return CalendarRegistry::gregorian()->toJulianDayNumber($this->year, $this->month, $this->day);
    }

    /**
     * Express the current date in the given calendar (or in the active
     * calendar when none is given).
     */
    public function toCalendarDate(string|CalendarInterface|null $calendar = null): CalendarDate
    {
        $calendar = null === $calendar ? $this->getCalendar() : CalendarRegistry::resolve($calendar);

        return CalendarDate::fromJulianDayNumber($calendar, $this->julianDayNumber());
    }

    public function toJalali(): CalendarDate
    {
        return $this->toCalendarDate('jalali');
    }

    public function toHijri(): CalendarDate
    {
        return $this->toCalendarDate('hijri');
    }

    public function toGregorianDate(): CalendarDate
    {
        return $this->toCalendarDate('gregorian');
    }

    // /////////////////////////////////////////////////////////////////
    // ///////////////////////// MODIFIERS //////////////////////////////
    // /////////////////////////////////////////////////////////////////

    /**
     * Set year/month/day expressed in the given calendar (active calendar
     * by default), keeping the time and timezone.
     */
    public function setCalendarDate(
        int $year,
        int $month,
        int $day,
        string|CalendarInterface|null $calendar = null,
    ): static {
        $calendar = null === $calendar ? $this->getCalendar() : CalendarRegistry::resolve($calendar);

        [$gregorianYear, $gregorianMonth, $gregorianDay] = CalendarRegistry::gregorian()
            ->fromJulianDayNumber($calendar->toJulianDayNumber($year, $month, $day));

        return $this->setDate($gregorianYear, $gregorianMonth, $gregorianDay);
    }

    /**
     * Add months in the active calendar, clamping the day to the length of
     * the target month (e.g. 1403-06-31 + 1 month = 1403-07-30 in Jalali).
     */
    public function addCalendarMonths(int $months = 1): static
    {
        $date = $this->toCalendarDate();
        $calendar = $date->calendar;
        $monthsPerYear = $calendar->monthsInYear($date->year);

        $index = ($date->year - 1) * $monthsPerYear + ($date->month - 1) + $months;
        $year = (int) floor($index / $monthsPerYear) + 1;
        $month = $index - ($year - 1) * $monthsPerYear + 1;
        $day = min($date->day, $calendar->daysInMonth($year, $month));

        return $this->setCalendarDate($year, $month, $day, $calendar);
    }

    public function subCalendarMonths(int $months = 1): static
    {
        return $this->addCalendarMonths(-$months);
    }

    public function addCalendarMonth(): static
    {
        return $this->addCalendarMonths(1);
    }

    public function subCalendarMonth(): static
    {
        return $this->addCalendarMonths(-1);
    }

    /**
     * Add years in the active calendar, clamping the day (e.g. Esfand 30 of
     * a leap Jalali year + 1 year = Esfand 29).
     */
    public function addCalendarYears(int $years = 1): static
    {
        $date = $this->toCalendarDate();
        $calendar = $date->calendar;

        $year = $date->year + $years;
        $day = min($date->day, $calendar->daysInMonth($year, $date->month));

        return $this->setCalendarDate($year, $date->month, $day, $calendar);
    }

    public function subCalendarYears(int $years = 1): static
    {
        return $this->addCalendarYears(-$years);
    }

    public function addCalendarYear(): static
    {
        return $this->addCalendarYears(1);
    }

    public function subCalendarYear(): static
    {
        return $this->addCalendarYears(-1);
    }

    public function startOfCalendarMonth(): static
    {
        $date = $this->toCalendarDate();

        return $this->setCalendarDate($date->year, $date->month, 1, $date->calendar)->startOfDay();
    }

    public function endOfCalendarMonth(): static
    {
        $date = $this->toCalendarDate();

        return $this
            ->setCalendarDate($date->year, $date->month, $date->daysInMonth(), $date->calendar)
            ->endOfDay();
    }

    public function startOfCalendarYear(): static
    {
        $date = $this->toCalendarDate();

        return $this->setCalendarDate($date->year, 1, 1, $date->calendar)->startOfDay();
    }

    public function endOfCalendarYear(): static
    {
        $date = $this->toCalendarDate();
        $lastMonth = $date->calendar->monthsInYear($date->year);
        $lastDay = $date->calendar->daysInMonth($date->year, $lastMonth);

        return $this->setCalendarDate($date->year, $lastMonth, $lastDay, $date->calendar)->endOfDay();
    }

    // /////////////////////////////////////////////////////////////////
    // ///////////////////////// INSPECTION /////////////////////////////
    // /////////////////////////////////////////////////////////////////

    public function isCalendarLeapYear(string|CalendarInterface|null $calendar = null): bool
    {
        return $this->toCalendarDate($calendar)->isLeapYear();
    }

    public function calendarDaysInMonth(string|CalendarInterface|null $calendar = null): int
    {
        return $this->toCalendarDate($calendar)->daysInMonth();
    }

    // /////////////////////////////////////////////////////////////////
    // ///////////////////////// FORMATTING /////////////////////////////
    // /////////////////////////////////////////////////////////////////

    /**
     * Format using PHP date() tokens where the date parts (Y y m n d j t L
     * z S F M l D N w) come from the active calendar and every other token
     * (time, timezone, ...) is delegated to Carbon.
     */
    public function calendarFormat(
        string $format,
        string|CalendarInterface|null $calendar = null,
        ?string $locale = null,
        bool $localizeDigits = false,
    ): string {
        $date = $this->toCalendarDate($calendar);
        $locale ??= static::getDefaultCalendarLocale();

        $output = '';
        $length = \strlen($format);

        for ($i = 0; $i < $length; ++$i) {
            $char = $format[$i];

            if ('\\' === $char) {
                $output .= $i + 1 < $length ? $format[++$i] : '';

                continue;
            }

            if (str_contains('YymndjtLzSFMlDNw', $char)) {
                $output .= $date->format($char, $locale);
            } elseif (ctype_alpha($char)) {
                $output .= $this->rawFormat($char);
            } else {
                $output .= $char;
            }
        }

        return $localizeDigits ? Digits::localize($output, $locale) : $output;
    }

    public function toCalendarDateString(string|CalendarInterface|null $calendar = null): string
    {
        return $this->calendarFormat('Y-m-d', $calendar);
    }

    public function toCalendarDateTimeString(string|CalendarInterface|null $calendar = null): string
    {
        return $this->calendarFormat('Y-m-d H:i:s', $calendar);
    }

    // /////////////////////////////////////////////////////////////////
    // ///////////////////////// MUTABILITY /////////////////////////////
    // /////////////////////////////////////////////////////////////////

    /**
     * Return a mutable Boron-flavored Carbon copy, keeping the active
     * calendar. Overrides Carbon's toMutable() so the underlying Carbon
     * machinery never leaks a plain Carbon\Carbon instance.
     */
    public function toMutable(): Carbon
    {
        return $this->cast(Carbon::class)->withCalendar($this->getActiveCalendar());
    }

    /**
     * Return an immutable Boron-flavored Carbon copy, keeping the active
     * calendar. Overrides Carbon's toImmutable() so the underlying Carbon
     * machinery never leaks a plain Carbon\CarbonImmutable instance.
     */
    public function toImmutable(): CarbonImmutable
    {
        return $this->cast(CarbonImmutable::class)->withCalendar($this->getActiveCalendar());
    }

    // /////////////////////////////////////////////////////////////////
    // //////////////// GLUE HELPERS (get/serialization) ////////////////
    // /////////////////////////////////////////////////////////////////

    /**
     * Calendar-aware magic properties: calendarYear, calendarMonth,
     * calendarDay, calendarDate, calendarMonthName, calendarDaysInMonth,
     * calendarDayOfYear, calendarName and julianDay.
     *
     * Returns null when $name is not a calendar property, so callers can
     * fall back to Carbon's own get().
     */
    protected function resolveCalendarProperty(Unit|string $name): mixed
    {
        if (!\is_string($name)) {
            return null;
        }

        return match ($name) {
            'calendarYear' => $this->toCalendarDate()->year,
            'calendarMonth' => $this->toCalendarDate()->month,
            'calendarDay' => $this->toCalendarDate()->day,
            'calendarDate' => $this->toCalendarDate(),
            'calendarMonthName' => $this->toCalendarDate()->getMonthName(static::getDefaultCalendarLocale()),
            'calendarDaysInMonth' => $this->toCalendarDate()->daysInMonth(),
            'calendarDayOfYear' => $this->toCalendarDate()->dayOfYear(),
            'calendarName' => $this->getCalendar()->getName(),
            'julianDay' => $this->julianDayNumber(),
            default => null,
        };
    }

    /**
     * Carbon serializes only date/timezone data, so the active calendar is
     * added on top (by name; unregistered custom calendar instances cannot
     * be carried through serialization).
     */
    protected function appendCalendarSerialization(array $data): array
    {
        $calendar = $this->activeCalendar;

        if ($calendar instanceof CalendarInterface) {
            $calendar = $calendar->getName();
        }

        if (\is_string($calendar) && CalendarRegistry::has($calendar)) {
            $data['boronCalendar'] = $calendar;
        }

        return $data;
    }

    protected function restoreCalendarSerialization(array $data): void
    {
        $calendar = $data['boronCalendar'] ?? null;

        if (\is_string($calendar) && CalendarRegistry::has($calendar)) {
            $this->activeCalendar = $calendar;
        }
    }
}
