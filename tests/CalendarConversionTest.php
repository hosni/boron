<?php

declare(strict_types=1);

namespace Boron\Tests;

use Boron\CalendarRegistry;
use Boron\Exceptions\InvalidCalendarDateException;
use Boron\Exceptions\UnsupportedCalendarRangeException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class CalendarConversionTest extends TestCase
{
    /**
     * @return iterable<string, array{string, array{int, int, int}, array{int, int, int}}>
     */
    public static function knownDates(): iterable
    {
        // [calendar, gregorian Y-m-d, calendar Y-m-d]
        yield 'nowruz 1403' => ['jalali', [2024, 3, 20], [1403, 1, 1]];
        yield 'nowruz 1404 (leap boundary)' => ['jalali', [2025, 3, 21], [1404, 1, 1]];
        yield 'esfand 30 1403' => ['jalali', [2025, 3, 20], [1403, 12, 30]];
        yield 'revolution day' => ['jalali', [1979, 2, 11], [1357, 11, 22]];
        yield 'mordad 19 1405' => ['jalali', [2026, 8, 10], [1405, 5, 19]];
        yield 'jalali epoch' => ['jalali', [622, 3, 21], [1, 1, 1]];
        yield 'ramadan 10 1445' => ['hijri', [2024, 3, 20], [1445, 9, 10]];
        yield 'hijri epoch' => ['hijri', [622, 7, 19], [1, 1, 1]];
        yield 'gregorian identity' => ['gregorian', [2024, 2, 29], [2024, 2, 29]];
    }

    #[DataProvider('knownDates')]
    public function testKnownDates(string $calendar, array $gregorian, array $expected): void
    {
        $target = CalendarRegistry::get($calendar);
        $julianDay = CalendarRegistry::gregorian()->toJulianDayNumber(...$gregorian);

        self::assertSame($expected, $target->fromJulianDayNumber($julianDay));
        self::assertSame($julianDay, $target->toJulianDayNumber(...$expected));
    }

    public function testJulianDayNumberConvention(): void
    {
        // Standard civil JDN: 1970-01-01 (Gregorian) is JDN 2440588.
        self::assertSame(2440588, CalendarRegistry::gregorian()->toJulianDayNumber(1970, 1, 1));
        // ICU PersianCalendar convention: Farvardin 1, year 1 is JDN 1948320.
        self::assertSame(1948320, CalendarRegistry::get('jalali')->toJulianDayNumber(1, 1, 1));
        // Civil (Friday) epoch of the tabular Islamic calendar: JDN 1948440.
        self::assertSame(1948440, CalendarRegistry::get('hijri')->toJulianDayNumber(1, 1, 1));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function arithmeticCalendars(): iterable
    {
        yield 'gregorian' => ['gregorian'];
        yield 'jalali' => ['jalali'];
        yield 'jalali-astronomical' => ['jalali-astronomical'];
        yield 'hijri' => ['hijri'];
    }

    #[DataProvider('arithmeticCalendars')]
    public function testRoundTrip(string $name): void
    {
        $calendar = CalendarRegistry::get($name);
        $gregorian = CalendarRegistry::gregorian();

        $start = $gregorian->toJulianDayNumber(1800, 1, 1);
        $end = $gregorian->toJulianDayNumber(2200, 12, 31);

        for ($julianDay = $start; $julianDay <= $end; $julianDay += 7) {
            [$year, $month, $day] = $calendar->fromJulianDayNumber($julianDay);

            self::assertTrue($calendar->isValidDate($year, $month, $day));
            self::assertSame(
                $julianDay,
                $calendar->toJulianDayNumber($year, $month, $day),
                sprintf('%s round trip failed for JDN %d (%d-%d-%d)', $name, $julianDay, $year, $month, $day),
            );
        }
    }

    #[DataProvider('arithmeticCalendars')]
    public function testConsecutiveDaysAreConsecutive(string $name): void
    {
        $calendar = CalendarRegistry::get($name);
        $julianDay = CalendarRegistry::gregorian()->toJulianDayNumber(2020, 1, 1);

        [$year, $month, $day] = $calendar->fromJulianDayNumber($julianDay);

        for ($i = 1; $i <= 800; $i++) {
            [$nextYear, $nextMonth, $nextDay] = $calendar->fromJulianDayNumber($julianDay + $i);

            $sameMonth = $nextYear === $year && $nextMonth === $month && $nextDay === $day + 1;
            $nextMonthStart = $nextDay === 1
                && $day === $calendar->daysInMonth($year, $month)
                && ($nextYear === $year && $nextMonth === $month + 1
                    || $nextYear === $year + 1 && $nextMonth === 1 && $month === $calendar->monthsInYear($year));

            self::assertTrue(
                $sameMonth || $nextMonthStart,
                sprintf('%s: %d-%d-%d is not followed by %d-%d-%d', $name, $year, $month, $day, $nextYear, $nextMonth, $nextDay),
            );

            [$year, $month, $day] = [$nextYear, $nextMonth, $nextDay];
        }
    }

    public function testJalaliLeapYears(): void
    {
        $jalali = CalendarRegistry::get('jalali');

        // 1403 is leap (Esfand has 30 days), 1404 is not: the exact case
        // where Birashk's 2820-year formula (used by date-object) fails.
        self::assertTrue($jalali->isLeapYear(1403));
        self::assertFalse($jalali->isLeapYear(1404));
        self::assertSame(30, $jalali->daysInMonth(1403, 12));
        self::assertSame(29, $jalali->daysInMonth(1404, 12));
        self::assertSame(366, $jalali->daysInYear(1403));

        foreach ([1370, 1375, 1379, 1387, 1391, 1395, 1399, 1408] as $leap) {
            self::assertTrue($jalali->isLeapYear($leap), "$leap should be leap");
        }

        foreach ([1400, 1401, 1402, 1405, 1406, 1407] as $common) {
            self::assertFalse($jalali->isLeapYear($common), "$common should not be leap");
        }
    }

    public function testHijriLeapYears(): void
    {
        $hijri = CalendarRegistry::get('hijri');

        self::assertTrue($hijri->isLeapYear(1442));  // 1442 % 30 = 2
        self::assertFalse($hijri->isLeapYear(1444));
        self::assertSame(355, $hijri->daysInYear(1442));
        self::assertSame(354, $hijri->daysInYear(1444));
        self::assertSame(30, $hijri->daysInMonth(1442, 12));
    }

    public function testAstronomicalVariantAgreesWithJalaliInModernRange(): void
    {
        $jalali = CalendarRegistry::get('jalali');
        $astronomical = CalendarRegistry::get('jalali-astronomical');

        // Known divergence years between the two drivers (1900-2100 AD):
        // the astronomical recurrence marks 1308, 1341 and 1473 as leap
        // while the 33-year cycle (and ICU) marks 1309, 1342 and 1474.
        $divergent = [1308, 1309, 1341, 1342, 1473, 1474];

        for ($year = 1300; $year <= 1450; $year++) {
            if (\in_array($year, $divergent, true)) {
                continue;
            }

            self::assertSame(
                $jalali->isLeapYear($year),
                $astronomical->isLeapYear($year),
                "Drivers disagree about year $year",
            );
        }

        // Both agree on the tricky 1403/1404 boundary.
        self::assertTrue($astronomical->isLeapYear(1403));
        self::assertFalse($astronomical->isLeapYear(1404));
        self::assertSame(
            $jalali->toJulianDayNumber(1403, 1, 1),
            $astronomical->toJulianDayNumber(1403, 1, 1),
        );
    }

    public function testInvalidDatesAreRejected(): void
    {
        $jalali = CalendarRegistry::get('jalali');

        self::assertFalse($jalali->isValidDate(1404, 12, 30)); // not leap
        self::assertTrue($jalali->isValidDate(1403, 12, 30));  // leap
        self::assertFalse($jalali->isValidDate(1403, 13, 1));
        self::assertFalse($jalali->isValidDate(1403, 0, 1));
        self::assertFalse($jalali->isValidDate(0, 1, 1));

        $this->expectException(InvalidCalendarDateException::class);
        $jalali->toJulianDayNumber(1404, 12, 30);
    }

    public function testDatesBeforeEpochAreRejected(): void
    {
        $this->expectException(UnsupportedCalendarRangeException::class);

        // 100 AD is before both the Jalali and Hijri epochs (622 AD).
        CalendarRegistry::get('jalali')->fromJulianDayNumber(
            CalendarRegistry::gregorian()->toJulianDayNumber(100, 1, 1),
        );
    }

    public function testMonthNames(): void
    {
        $jalali = CalendarRegistry::get('jalali');

        self::assertSame('Farvardin', $jalali->getMonthName(1));
        self::assertSame('فروردین', $jalali->getMonthName(1, 'fa'));
        self::assertSame('Esfand', $jalali->getMonthName(12));

        $hijri = CalendarRegistry::get('hijri');

        self::assertSame('Ramadan', $hijri->getMonthName(9));
        self::assertSame('رمضان', $hijri->getMonthName(9, 'ar'));
        self::assertCount(12, $hijri->getMonthNames('fa'));
    }
}
