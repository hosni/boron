<?php

declare(strict_types=1);

namespace Boron\Tests;

use Boron\Boron;
use Boron\CalendarDate;
use Boron\CalendarRegistry;
use Boron\Exceptions\InvalidCalendarDateException;
use PHPUnit\Framework\TestCase;

final class CalendarDateTest extends TestCase
{
    public function testConstructionValidates(): void
    {
        $this->expectException(InvalidCalendarDateException::class);

        new CalendarDate(CalendarRegistry::get('jalali'), 1404, 12, 30);
    }

    public function testConversionBetweenCalendars(): void
    {
        $jalali = new CalendarDate(CalendarRegistry::get('jalali'), 1403, 1, 1);
        $hijri = $jalali->to('hijri');

        self::assertSame('1445-09-10', (string) $hijri);
        self::assertSame('2024-03-20', (string) $jalali->to('gregorian'));
        self::assertTrue($jalali->equalTo($hijri));
        self::assertSame($jalali, $jalali->to('jalali'));
    }

    public function testProperties(): void
    {
        $date = new CalendarDate(CalendarRegistry::get('jalali'), 1403, 12, 30);

        self::assertTrue($date->isLeapYear());
        self::assertSame(30, $date->daysInMonth());
        self::assertSame(366, $date->daysInYear());
        self::assertSame(366, $date->dayOfYear());
        self::assertSame('Esfand', $date->getMonthName());
        self::assertSame('اسفند', $date->getMonthName('fa'));
    }

    public function testFormat(): void
    {
        $date = new CalendarDate(CalendarRegistry::get('jalali'), 1403, 1, 1);

        self::assertSame('1403/01/01', $date->format('Y/m/d'));
        self::assertSame('1 Farvardin 1403', $date->format('j F Y'));
        self::assertSame('Wednesday', $date->format('l'));
        self::assertSame('چهارشنبه ۱ فروردین ۱۴۰۳', $date->format('l j F Y', 'fa', true));
        self::assertSame('Year: 1403', $date->format('\Y\e\a\r: Y'));
    }

    public function testAddDays(): void
    {
        $date = new CalendarDate(CalendarRegistry::get('jalali'), 1403, 12, 29);

        self::assertSame('1403-12-30', (string) $date->addDays(1));
        self::assertSame('1404-01-01', (string) $date->addDays(2));
        self::assertSame('1403-12-28', (string) $date->addDays(-1));
    }

    public function testToBoron(): void
    {
        $date = new CalendarDate(CalendarRegistry::get('jalali'), 1403, 1, 1);

        self::assertSame('2024-03-20 00:00:00', $date->toBoron('UTC')->toDateTimeString());
        self::assertInstanceOf(Boron::class, $date->toBoron());
        self::assertSame('2024-03-20', $date->toBoronImmutable('Asia/Tehran')->toDateString());
    }

    public function testJson(): void
    {
        $date = new CalendarDate(CalendarRegistry::get('jalali'), 1403, 1, 1);

        self::assertSame(
            '{"calendar":"jalali","year":1403,"month":1,"day":1}',
            json_encode($date),
        );
    }
}
