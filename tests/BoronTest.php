<?php

declare(strict_types=1);

namespace Boron\Tests;

use Carbon\CarbonInterface;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Boron\Boron;
use Boron\BoronImmutable;
use Boron\BoronInterface;
use Boron\BoronMutable;
use Boron\CalendarDate;
use Boron\Carbon;
use Boron\CarbonImmutable;
use Boron\Exceptions\InvalidFormatException;
use PHPUnit\Framework\TestCase;

final class BoronTest extends TestCase
{
    protected function setUp(): void
    {
        Boron::setDefaultCalendar('gregorian');
        Boron::setDefaultCalendarLocale('en');
        Boron::setTestNow();
    }

    protected function tearDown(): void
    {
        Boron::setDefaultCalendar('gregorian');
        Boron::setDefaultCalendarLocale('en');
        Boron::setTestNow();
    }

    public function testClassHierarchy(): void
    {
        // Standalone family: NOT Carbon subclasses, built like Carbon itself.
        self::assertInstanceOf(DateTime::class, Boron::now());
        self::assertNotInstanceOf(\Carbon\Carbon::class, Boron::now());
        self::assertInstanceOf(Boron::class, BoronMutable::now());
        self::assertInstanceOf(DateTimeImmutable::class, BoronImmutable::now());
        self::assertNotInstanceOf(\Carbon\CarbonImmutable::class, BoronImmutable::now());

        // All four classes fulfill both interfaces.
        foreach ([Boron::now(), BoronImmutable::now(), Carbon::now(), CarbonImmutable::now()] as $date) {
            self::assertInstanceOf(BoronInterface::class, $date);
            self::assertInstanceOf(CarbonInterface::class, $date);
            self::assertInstanceOf(DateTimeInterface::class, $date);
        }

        // Mutability flags, like Carbon's.
        self::assertTrue(Boron::isMutable());
        self::assertTrue(BoronMutable::isMutable());
        self::assertFalse(BoronImmutable::isMutable());
        self::assertTrue(BoronImmutable::isImmutable());
    }

    public function testCarbonCompatibility(): void
    {
        $now = Boron::now();

        // Whole Carbon API is available through Carbon's Date trait.
        self::assertInstanceOf(Boron::class, $now->copy()->addDays(3)->subMonth()->startOfWeek());

        // Interop: Boron accepts plain Carbon instances and vice versa.
        $carbon = \Carbon\Carbon::parse('2024-03-20 12:00:00', 'UTC');
        $boron = Boron::instance($carbon);

        self::assertTrue($boron->equalTo($carbon));
        self::assertSame('1403-01-01', $boron->toJalali()->__toString());

        // Carbon's own calendar() (moment-style display) is untouched.
        self::assertIsString(Boron::now()->calendar());
        self::assertSame('3 weeks before', Boron::parse('2024-03-01')->diffForHumans('2024-03-22'));
    }

    public function testConversionGetters(): void
    {
        $date = Boron::parse('2024-03-20 15:30:45', 'Asia/Tehran');

        self::assertSame('1403-01-01', (string) $date->toJalali());
        self::assertSame('1445-09-10', (string) $date->toHijri());
        self::assertSame('2024-03-20', (string) $date->toGregorianDate());
        self::assertSame(2460390, $date->julianDayNumber());
    }

    public function testActiveCalendar(): void
    {
        $date = Boron::parse('2024-03-20')->withCalendar('jalali');

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
            Boron::parse('2024-03-20')->withCalendar('shamsi')->getCalendar(),
            $date->getCalendar(),
        );

        // Plain Carbon getters still see the Gregorian date.
        self::assertSame(2024, $date->year);
        self::assertSame(3, $date->month);
    }

    public function testDefaultCalendar(): void
    {
        Boron::setDefaultCalendar('jalali');

        $date = Boron::parse('2024-03-20');

        self::assertSame('jalali', $date->getCalendar()->getName());
        self::assertSame('1403-01-01', $date->toCalendarDateString());

        // Shared between mutable and immutable classes.
        self::assertSame('jalali', BoronImmutable::parse('2024-03-20')->getCalendar()->getName());
    }

    public function testCreationFromCalendars(): void
    {
        self::assertSame('2024-03-20', Boron::fromJalali(1403, 1, 1)->toDateString());
        self::assertSame('2024-03-20', Boron::fromHijri(1445, 9, 10)->toDateString());
        self::assertSame(
            '2024-03-20 23:59:00',
            Boron::fromCalendar('jalali', 1403, 1, 1, 23, 59)->toDateTimeString(),
        );

        // Instances created from a calendar keep it active.
        self::assertSame('jalali', Boron::fromJalali(1403, 1, 1)->getCalendar()->getName());

        // Timezone support.
        $tehran = Boron::fromJalali(1403, 1, 1, 0, 0, 0, 'Asia/Tehran');
        self::assertSame('Asia/Tehran', $tehran->timezoneName);
    }

    public function testParseFromCalendar(): void
    {
        self::assertSame('2024-03-20', Boron::parseFromCalendar('jalali', '1403/01/01')->toDateString());
        self::assertSame('2024-03-20', Boron::parseFromCalendar('jalali', '1403-1-1')->toDateString());
        self::assertSame(
            '2024-03-20 14:30:00',
            Boron::parseFromCalendar('jalali', '1403/01/01 14:30')->toDateTimeString(),
        );

        // Persian digits are accepted.
        self::assertSame('2024-03-20', Boron::parseFromCalendar('jalali', '۱۴۰۳/۰۱/۰۱')->toDateString());

        $this->expectException(InvalidFormatException::class);
        Boron::parseFromCalendar('jalali', 'not a date');
    }

    public function testSetCalendarDateKeepsTime(): void
    {
        $date = Boron::parse('2020-01-01 13:45:30', 'Asia/Tehran')
            ->setCalendarDate(1403, 1, 1, 'jalali');

        self::assertSame('2024-03-20 13:45:30', $date->toDateTimeString());
        self::assertSame('Asia/Tehran', $date->timezoneName);
    }

    public function testCalendarArithmetic(): void
    {
        // Shahrivar 31 + 1 month clamps to Mehr 30.
        $date = Boron::fromJalali(1403, 6, 31);
        self::assertSame('1403-07-30', $date->copy()->addCalendarMonths(1)->toCalendarDateString());

        // Crossing the year boundary.
        self::assertSame('1404-01-31', $date->copy()->addCalendarMonths(7)->toCalendarDateString());
        self::assertSame('1402-06-31', $date->copy()->subCalendarYears(1)->toCalendarDateString());

        // Esfand 30 (leap 1403) + 1 year clamps to Esfand 29.
        self::assertSame(
            '1404-12-29',
            Boron::fromJalali(1403, 12, 30)->addCalendarYears(1)->toCalendarDateString(),
        );

        // Negative months.
        self::assertSame(
            '1402-12-29',
            Boron::fromJalali(1403, 1, 30)->subCalendarMonths(1)->toCalendarDateString(),
        );
    }

    public function testStartAndEndOfCalendarPeriods(): void
    {
        $date = Boron::parse('2024-09-15 12:00:00')->withCalendar('jalali'); // 1403-06-25

        self::assertSame('1403-06-01', $date->copy()->startOfCalendarMonth()->toCalendarDateString());
        self::assertSame('00:00:00', $date->copy()->startOfCalendarMonth()->toTimeString());
        self::assertSame('1403-06-31', $date->copy()->endOfCalendarMonth()->toCalendarDateString());
        self::assertSame('23:59:59', $date->copy()->endOfCalendarMonth()->toTimeString());
        self::assertSame('1403-01-01', $date->copy()->startOfCalendarYear()->toCalendarDateString());
        self::assertSame('1403-12-30', $date->copy()->endOfCalendarYear()->toCalendarDateString());
    }

    public function testCalendarFormat(): void
    {
        $date = Boron::parse('2024-03-20 14:05:09')->withCalendar('jalali');

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

    public function testImmutableVariant(): void
    {
        $date = BoronImmutable::parse('2024-03-20')->withCalendar('jalali');

        self::assertInstanceOf(DateTimeImmutable::class, $date);

        $next = $date->addDay();

        self::assertNotSame($date, $next);
        self::assertSame('1403-01-01', $date->toCalendarDateString());
        self::assertSame('1403-01-02', $next->toCalendarDateString());

        // The active calendar survives immutable modifications.
        self::assertSame('jalali', $next->getCalendar()->getName());

        // withCalendar() does not mutate immutable instances.
        $hijri = $date->withCalendar('hijri');
        self::assertSame('jalali', $date->getCalendar()->getName());
        self::assertSame('hijri', $hijri->getCalendar()->getName());
    }

    public function testMutableToImmutableAndBack(): void
    {
        // CarbonInterface pins toImmutable(): CarbonImmutable and
        // toMutable(): Carbon, so these conversions return the drop-in
        // (Carbon-extending) Boron classes — never plain Carbon.
        $mutable = Boron::parse('2024-03-20')->withCalendar('jalali');
        $immutable = $mutable->toImmutable();

        self::assertInstanceOf(CarbonImmutable::class, $immutable);
        self::assertInstanceOf(BoronInterface::class, $immutable);
        self::assertSame('jalali', $immutable->getCalendar()->getName());
        self::assertTrue($immutable->equalTo($mutable));

        $back = $immutable->toMutable();

        self::assertInstanceOf(Carbon::class, $back);
        self::assertSame('jalali', $back->getCalendar()->getName());

        // Same guarantee from the BoronImmutable side.
        self::assertInstanceOf(Carbon::class, BoronImmutable::now()->toMutable());
    }

    public function testSerializationKeepsCalendar(): void
    {
        $date = Boron::parse('2024-03-20 10:00:00', 'Asia/Tehran')->withCalendar('jalali');
        $copy = unserialize(serialize($date));

        self::assertTrue($copy->equalTo($date));
        self::assertSame('jalali', $copy->getCalendar()->getName());
        self::assertSame('1403-01-01', $copy->toCalendarDateString());
    }

    public function testIsCalendarLeapYear(): void
    {
        self::assertTrue(Boron::parse('2025-03-01')->isCalendarLeapYear('jalali'));  // 1403
        self::assertFalse(Boron::parse('2025-04-01')->isCalendarLeapYear('jalali')); // 1404
        self::assertSame(30, Boron::parse('2025-03-01')->calendarDaysInMonth('jalali')); // Esfand 1403
    }

    public function testTestNowWorksThroughCarbon(): void
    {
        Boron::setTestNow('2024-03-20 12:00:00');

        self::assertSame('1403-01-01', Boron::now()->toJalali()->__toString());
        self::assertSame('1403-01-01', BoronImmutable::now()->toJalali()->__toString());
    }
}
