<?php

declare(strict_types=1);

namespace Boron\Tests;

use Boron\CalendarDate;
use Boron\Carbon;
use Boron\CarbonImmutable;
use Boron\CarbonInterface;
use Boron\Exceptions\InvalidFormatException;
use DateTimeInterface;
use PHPUnit\Framework\TestCase;

/**
 * Behavioral tests of the multi-calendar API on Boron\Carbon and
 * Boron\CarbonImmutable (creation, conversion, arithmetic, formatting,
 * parsing, serialization).
 */
final class CalendarApiTest extends TestCase
{
    protected function setUp(): void
    {
        Carbon::setDefaultCalendar('gregorian');
        Carbon::setDefaultCalendarLocale('en');
        Carbon::setTestNow();
    }

    protected function tearDown(): void
    {
        Carbon::setDefaultCalendar('gregorian');
        Carbon::setDefaultCalendarLocale('en');
        Carbon::setTestNow();
    }

    public function testInterfaces(): void
    {
        foreach ([Carbon::now(), CarbonImmutable::now()] as $date) {
            self::assertInstanceOf(CarbonInterface::class, $date);
            self::assertInstanceOf(\Carbon\CarbonInterface::class, $date);
            self::assertInstanceOf(DateTimeInterface::class, $date);
        }

        self::assertTrue(Carbon::isMutable());
        self::assertFalse(CarbonImmutable::isMutable());
        self::assertTrue(CarbonImmutable::isImmutable());
    }

    public function testCarbonCompatibility(): void
    {
        // Whole Carbon API is available.
        self::assertInstanceOf(Carbon::class, Carbon::now()->copy()->addDays(3)->subMonth()->startOfWeek());

        // Interop: Boron accepts plain Carbon instances and vice versa.
        $carbon = \Carbon\Carbon::parse('2024-03-20 12:00:00', 'UTC');
        $boron = Carbon::instance($carbon);

        self::assertTrue($boron->equalTo($carbon));
        self::assertSame('1403-01-01', $boron->toJalali()->__toString());

        // Carbon's own calendar() (moment-style display) is untouched.
        self::assertIsString(Carbon::now()->calendar());
        self::assertSame('3 weeks before', Carbon::parse('2024-03-01')->diffForHumans('2024-03-22'));
    }

    public function testConversionGetters(): void
    {
        $date = Carbon::parse('2024-03-20 15:30:45', 'Asia/Tehran');

        self::assertSame('1403-01-01', (string) $date->toJalali());
        self::assertSame('1445-09-10', (string) $date->toHijri());
        self::assertSame('2024-03-20', (string) $date->toGregorianDate());
        self::assertSame(2460390, $date->julianDayNumber());
    }

    public function testActiveCalendar(): void
    {
        $date = Carbon::parse('2024-03-20')->withCalendar('jalali');

        self::assertSame('jalali', $date->getCalendar()->getName());
        self::assertSame('1403-01-01', $date->toCalendarDateString());
        self::assertSame(1403, $date->calendarYear);
        self::assertSame(1, $date->calendarMonth);
        self::assertSame(1, $date->calendarDay);
        self::assertSame('Farvardin', $date->calendarMonthName);
        self::assertSame(31, $date->calendarDaysInMonth);
        self::assertSame(1, $date->calendarDayOfYear);
        self::assertSame('jalali', $date->calendarName);
        self::assertSame(2460390, $date->julianDay);
        self::assertInstanceOf(CalendarDate::class, $date->calendarDate);

        // Aliases resolve to the same driver.
        self::assertSame(
            Carbon::parse('2024-03-20')->withCalendar('shamsi')->getCalendar(),
            $date->getCalendar(),
        );

        // Plain Carbon getters still see the Gregorian date.
        self::assertSame(2024, $date->year);
        self::assertSame(3, $date->month);
    }

    public function testDefaultCalendar(): void
    {
        Carbon::setDefaultCalendar('jalali');

        $date = Carbon::parse('2024-03-20');

        self::assertSame('jalali', $date->getCalendar()->getName());
        self::assertSame('1403-01-01', $date->toCalendarDateString());

        // Shared between mutable and immutable classes.
        self::assertSame('jalali', CarbonImmutable::parse('2024-03-20')->getCalendar()->getName());
    }

    public function testCreationFromCalendars(): void
    {
        self::assertSame('2024-03-20', Carbon::fromJalali(1403, 1, 1)->toDateString());
        self::assertSame('2024-03-20', Carbon::fromHijri(1445, 9, 10)->toDateString());
        self::assertSame(
            '2024-03-20 23:59:00',
            Carbon::fromCalendar('jalali', 1403, 1, 1, 23, 59)->toDateTimeString(),
        );

        // Instances created from a calendar keep it active.
        self::assertSame('jalali', Carbon::fromJalali(1403, 1, 1)->getCalendar()->getName());

        // Timezone support.
        $tehran = Carbon::fromJalali(1403, 1, 1, 0, 0, 0, 'Asia/Tehran');
        self::assertSame('Asia/Tehran', $tehran->timezoneName);
    }

    public function testParseFromCalendar(): void
    {
        self::assertSame('2024-03-20', Carbon::parseFromCalendar('jalali', '1403/01/01')->toDateString());
        self::assertSame('2024-03-20', Carbon::parseFromCalendar('jalali', '1403-1-1')->toDateString());
        self::assertSame(
            '2024-03-20 14:30:00',
            Carbon::parseFromCalendar('jalali', '1403/01/01 14:30')->toDateTimeString(),
        );

        // Persian digits are accepted.
        self::assertSame('2024-03-20', Carbon::parseFromCalendar('jalali', '۱۴۰۳/۰۱/۰۱')->toDateString());

        $this->expectException(InvalidFormatException::class);
        Carbon::parseFromCalendar('jalali', 'not a date');
    }

    public function testSetCalendarDateKeepsTime(): void
    {
        $date = Carbon::parse('2020-01-01 13:45:30', 'Asia/Tehran')
            ->setCalendarDate(1403, 1, 1, 'jalali');

        self::assertSame('2024-03-20 13:45:30', $date->toDateTimeString());
        self::assertSame('Asia/Tehran', $date->timezoneName);
    }

    public function testCalendarArithmetic(): void
    {
        // Shahrivar 31 + 1 month clamps to Mehr 30.
        $date = Carbon::fromJalali(1403, 6, 31);
        self::assertSame('1403-07-30', $date->copy()->addCalendarMonths(1)->toCalendarDateString());

        // Crossing the year boundary.
        self::assertSame('1404-01-31', $date->copy()->addCalendarMonths(7)->toCalendarDateString());
        self::assertSame('1402-06-31', $date->copy()->subCalendarYears(1)->toCalendarDateString());

        // Esfand 30 (leap 1403) + 1 year clamps to Esfand 29.
        self::assertSame(
            '1404-12-29',
            Carbon::fromJalali(1403, 12, 30)->addCalendarYears(1)->toCalendarDateString(),
        );

        // Negative months.
        self::assertSame(
            '1402-12-29',
            Carbon::fromJalali(1403, 1, 30)->subCalendarMonths(1)->toCalendarDateString(),
        );
    }

    public function testStartAndEndOfCalendarPeriods(): void
    {
        $date = Carbon::parse('2024-09-15 12:00:00')->withCalendar('jalali'); // 1403-06-25

        self::assertSame('1403-06-01', $date->copy()->startOfCalendarMonth()->toCalendarDateString());
        self::assertSame('00:00:00', $date->copy()->startOfCalendarMonth()->toTimeString());
        self::assertSame('1403-06-31', $date->copy()->endOfCalendarMonth()->toCalendarDateString());
        self::assertSame('23:59:59', $date->copy()->endOfCalendarMonth()->toTimeString());
        self::assertSame('1403-01-01', $date->copy()->startOfCalendarYear()->toCalendarDateString());
        self::assertSame('1403-12-30', $date->copy()->endOfCalendarYear()->toCalendarDateString());
    }

    public function testCalendarFormat(): void
    {
        $date = Carbon::parse('2024-03-20 14:05:09')->withCalendar('jalali');

        self::assertSame('1403/01/01', $date->calendarFormat('Y/m/d'));
        self::assertSame('1 Farvardin 1403', $date->calendarFormat('j F Y'));
        self::assertSame('1st of Farvardin', $date->calendarFormat('jS \o\f F'));
        self::assertSame('1403-01-01 14:05:09', $date->calendarFormat('Y-m-d H:i:s'));
        self::assertSame('۱ فروردین ۱۴۰۳', $date->calendarFormat('j F Y', locale: 'fa', localizeDigits: true));
        self::assertSame('10 Ramadan 1445', $date->calendarFormat('j F Y', 'hijri'));

        // Weekday: 2024-03-20 was a Wednesday.
        self::assertSame('Wednesday', $date->calendarFormat('l'));
        self::assertSame('چهارشنبه', $date->calendarFormat('l', locale: 'fa'));

        // Leap flag and days-in-month come from the active calendar.
        self::assertSame('1', $date->calendarFormat('L'));
        self::assertSame('31', $date->calendarFormat('t'));
    }

    public function testIsCalendarLeapYear(): void
    {
        self::assertTrue(Carbon::parse('2025-03-01')->isCalendarLeapYear('jalali'));  // 1403
        self::assertFalse(Carbon::parse('2025-04-01')->isCalendarLeapYear('jalali')); // 1404
        self::assertSame(30, Carbon::parse('2025-03-01')->calendarDaysInMonth('jalali')); // Esfand 1403
    }

    public function testTestNowWorksThroughCarbon(): void
    {
        Carbon::setTestNow('2024-03-20 12:00:00');

        self::assertSame('1403-01-01', Carbon::now()->toJalali()->__toString());
        self::assertSame('1403-01-01', CarbonImmutable::now()->toJalali()->__toString());
    }
}
