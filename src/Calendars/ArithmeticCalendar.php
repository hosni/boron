<?php

declare(strict_types=1);

namespace Boron\Calendars;

use Boron\Exceptions\InvalidCalendarDateException;
use Boron\Exceptions\UnsupportedCalendarRangeException;

/**
 * Base class for pure-PHP arithmetic calendars.
 *
 * The engine is a port of Shahab Yazdi's date-object library
 * (https://github.com/shahabyazdi/date-object): a calendar is fully described
 * by its epoch, its leap-year rule and its month lengths, and every
 * conversion goes through the Julian Day Number.
 *
 * Note on epochs: date-object uses a day count that is off by one from the
 * standard civil JDN convention. Boron epochs are aligned with the standard
 * convention (and therefore with ICU), so `epoch() + 1` is the JDN of
 * year 1, month 1, day 1 of the calendar.
 *
 * Only years >= 1 are supported; instants before the calendar epoch throw
 * an UnsupportedCalendarRangeException.
 */
abstract class ArithmeticCalendar implements CalendarInterface
{
    /**
     * @return list<int> the length of each month for a common/leap year
     */
    abstract public function getMonthLengths(bool $leapYear): array;

    abstract public function isLeapYear(int $year): bool;

    public function monthsInYear(int $year): int
    {
        return 12;
    }

    public function daysInMonth(int $year, int $month): int
    {
        $lengths = $this->getMonthLengths($this->isLeapYear($year));

        if ($month < 1 || $month > \count($lengths)) {
            throw InvalidCalendarDateException::forDate($this->getName(), $year, $month, 1);
        }

        return $lengths[$month - 1];
    }

    public function daysInYear(int $year): int
    {
        return $this->commonYearLength() + ($this->isLeapYear($year) ? 1 : 0);
    }

    public function dayOfYear(int $year, int $month, int $day): int
    {
        $lengths = $this->getMonthLengths($this->isLeapYear($year));

        for ($i = 0; $i < $month - 1; ++$i) {
            $day += $lengths[$i];
        }

        return $day;
    }

    public function isValidDate(int $year, int $month, int $day): bool
    {
        return $year >= 1
            && $month >= 1
            && $month <= $this->monthsInYear($year)
            && $day >= 1
            && $day <= $this->daysInMonth($year, $month);
    }

    public function toJulianDayNumber(int $year, int $month, int $day): int
    {
        if (!$this->isValidDate($year, $month, $day)) {
            throw InvalidCalendarDateException::forDate($this->getName(), $year, $month, $day);
        }

        return $this->epoch()
            + $this->commonYearLength() * ($year - 1)
            + $this->leapYearsBefore($year)
            + $this->dayOfYear($year, $month, $day);
    }

    public function fromJulianDayNumber(int $julianDayNumber): array
    {
        $days = $julianDayNumber - $this->epoch();

        if ($days < 1) {
            throw UnsupportedCalendarRangeException::beforeEpoch($this->getName());
        }

        $year = max(1, (int) (($days + 0.5) / $this->averageYearLength()) + 1);

        while (true) {
            $dayOfYear = $days
                - $this->commonYearLength() * ($year - 1)
                - $this->leapYearsBefore($year);

            if ($dayOfYear < 1) {
                --$year;

                continue;
            }

            if ($dayOfYear > $this->daysInYear($year)) {
                ++$year;

                continue;
            }

            break;
        }

        $month = 1;

        foreach ($this->getMonthLengths($this->isLeapYear($year)) as $length) {
            if ($dayOfYear <= $length) {
                break;
            }

            $dayOfYear -= $length;
            ++$month;
        }

        return [$year, $month, $dayOfYear];
    }

    public function getMonthName(int $month, string $locale = 'en'): string
    {
        $names = $this->getMonthNames($locale);

        if ($month < 1 || $month > \count($names)) {
            throw InvalidCalendarDateException::forDate($this->getName(), 1, $month, 1);
        }

        return $names[$month - 1];
    }

    /**
     * Number of days in a common (non-leap) year.
     */
    abstract protected function commonYearLength(): int;

    /**
     * JDN of the day before year 1, month 1, day 1.
     */
    abstract protected function epoch(): int;

    /**
     * Average year length, used to guess the year of a day count.
     */
    abstract protected function averageYearLength(): float;

    /**
     * Number of leap years in the range [1, $year - 1].
     */
    abstract protected function leapYearsBefore(int $year): int;
}
