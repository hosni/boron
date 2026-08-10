<?php

declare(strict_types=1);

namespace Boron\Calendars;

use Boron\Exceptions\IntlExtensionNotLoadedException;
use Boron\Exceptions\InvalidCalendarDateException;
use Boron\Exceptions\UnknownCalendarException;
use IntlCalendar;
use IntlDateFormatter;

/**
 * Calendar driver backed by the PHP intl extension (ICU).
 *
 * Any ICU calendar keyword can be used: "persian", "islamic-civil",
 * "islamic-umalqura", "islamic", "gregorian", "buddhist", "hebrew", ...
 *
 * ICU exposes a JULIAN_DAY field which uses the same civil JDN convention
 * as Boron's arithmetic calendars, so both driver families are freely
 * interchangeable.
 */
class IcuCalendar implements CalendarInterface
{
    private IntlCalendar $prototype;

    /**
     * @param string      $icuType ICU calendar keyword, e.g. "persian" or "islamic-umalqura"
     * @param string|null $name    Boron name of this calendar; defaults to the ICU keyword
     */
    public function __construct(
        private readonly string $icuType,
        private readonly ?string $name = null,
    ) {
        if (!\extension_loaded('intl')) {
            throw IntlExtensionNotLoadedException::forCalendar($name ?? $icuType);
        }

        $calendar = IntlCalendar::createInstance('UTC', 'en_US@calendar='.$icuType);

        if (null === $calendar || ('gregorian' !== $icuType && 'gregorian' === $calendar->getType())) {
            throw UnknownCalendarException::forName($icuType.' (ICU)');
        }

        if ($calendar instanceof \IntlGregorianCalendar) {
            // Make ICU's hybrid Julian/Gregorian calendar proleptic so it
            // agrees with Boron's arithmetic Gregorian for dates before the
            // 1582 cutover.
            $calendar->setGregorianChange(-1.0e15);
        }

        $this->prototype = $calendar;
    }

    public function getName(): string
    {
        return $this->name ?? $this->icuType;
    }

    public function getIcuType(): string
    {
        return $this->icuType;
    }

    public function toJulianDayNumber(int $year, int $month, int $day): int
    {
        if (!$this->isValidDate($year, $month, $day)) {
            throw InvalidCalendarDateException::forDate($this->getName(), $year, $month, $day);
        }

        return $this->calendarAt($year, $month, $day)->get(IntlCalendar::FIELD_JULIAN_DAY);
    }

    public function fromJulianDayNumber(int $julianDayNumber): array
    {
        $calendar = clone $this->prototype;
        $calendar->clear();
        $calendar->set(IntlCalendar::FIELD_JULIAN_DAY, $julianDayNumber);

        return [
            $calendar->get(IntlCalendar::FIELD_EXTENDED_YEAR),
            $calendar->get(IntlCalendar::FIELD_MONTH) + 1,
            $calendar->get(IntlCalendar::FIELD_DAY_OF_MONTH),
        ];
    }

    public function isLeapYear(int $year): bool
    {
        // A year is leap when it is longer than the calendar's common year:
        // >= 366 days for solar calendars, >= 355 for lunar ones.
        $days = $this->daysInYear($year);

        return $days >= ($days > 360 ? 366 : 355);
    }

    public function daysInMonth(int $year, int $month): int
    {
        return $this->calendarAt($year, $month, 1)
            ->getActualMaximum(IntlCalendar::FIELD_DAY_OF_MONTH);
    }

    public function daysInYear(int $year): int
    {
        return $this->calendarAt($year, 1, 1)
            ->getActualMaximum(IntlCalendar::FIELD_DAY_OF_YEAR);
    }

    public function monthsInYear(int $year): int
    {
        return $this->calendarAt($year, 1, 1)
            ->getActualMaximum(IntlCalendar::FIELD_MONTH) + 1;
    }

    public function dayOfYear(int $year, int $month, int $day): int
    {
        return $this->calendarAt($year, $month, $day)
            ->get(IntlCalendar::FIELD_DAY_OF_YEAR);
    }

    public function isValidDate(int $year, int $month, int $day): bool
    {
        if ($year < 1 || $month < 1 || $day < 1) {
            return false;
        }

        $calendar = $this->calendarAt($year, $month, $day);

        // ICU normalizes out-of-range fields; a date is valid when it
        // round-trips unchanged.
        return $calendar->get(IntlCalendar::FIELD_EXTENDED_YEAR) === $year
            && $calendar->get(IntlCalendar::FIELD_MONTH) + 1 === $month
            && $calendar->get(IntlCalendar::FIELD_DAY_OF_MONTH) === $day;
    }

    public function getMonthNames(string $locale = 'en'): array
    {
        $formatter = new IntlDateFormatter(
            $locale.'@calendar='.$this->icuType,
            IntlDateFormatter::FULL,
            IntlDateFormatter::NONE,
            'UTC',
            IntlDateFormatter::TRADITIONAL,
            'MMMM',
        );

        $names = [];
        $months = $this->monthsInYear(1400);

        for ($month = 1; $month <= $months; ++$month) {
            $names[] = (string) $formatter->format($this->calendarAt(1400, $month, 1));
        }

        return $names;
    }

    public function getMonthName(int $month, string $locale = 'en'): string
    {
        $names = $this->getMonthNames($locale);

        if ($month < 1 || $month > \count($names)) {
            throw InvalidCalendarDateException::forDate($this->getName(), 1, $month, 1);
        }

        return $names[$month - 1];
    }

    private function calendarAt(int $year, int $month, int $day): IntlCalendar
    {
        $calendar = clone $this->prototype;
        $calendar->clear();
        $calendar->set(IntlCalendar::FIELD_EXTENDED_YEAR, $year);
        $calendar->set(IntlCalendar::FIELD_MONTH, $month - 1);
        $calendar->set(IntlCalendar::FIELD_DAY_OF_MONTH, $day);

        return $calendar;
    }
}
