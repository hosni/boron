<?php

declare(strict_types=1);

namespace Boron\Tests;

use Boron\CalendarRegistry;
use Boron\Calendars\CalendarInterface;
use Boron\Calendars\JalaliCalendar;
use Boron\Exceptions\UnknownCalendarException;
use PHPUnit\Framework\TestCase;

final class CalendarRegistryTest extends TestCase
{
    protected function tearDown(): void
    {
        CalendarRegistry::setDefaultCalendar('gregorian');
    }

    public function testBuiltInCalendars(): void
    {
        foreach (['gregorian', 'jalali', 'jalali-astronomical', 'hijri'] as $name) {
            self::assertInstanceOf(CalendarInterface::class, CalendarRegistry::get($name));
        }
    }

    public function testAliases(): void
    {
        self::assertSame(CalendarRegistry::get('jalali'), CalendarRegistry::get('persian'));
        self::assertSame(CalendarRegistry::get('jalali'), CalendarRegistry::get('shamsi'));
        self::assertSame(CalendarRegistry::get('jalali'), CalendarRegistry::get('Jalali'));
        self::assertSame(CalendarRegistry::get('hijri'), CalendarRegistry::get('islamic'));
        self::assertSame(CalendarRegistry::get('hijri'), CalendarRegistry::get('arabic'));
    }

    public function testInstancesAreSingletons(): void
    {
        self::assertSame(CalendarRegistry::get('jalali'), CalendarRegistry::get('jalali'));
    }

    public function testUnknownCalendarThrows(): void
    {
        self::assertFalse(CalendarRegistry::has('klingon'));

        $this->expectException(UnknownCalendarException::class);
        CalendarRegistry::get('klingon');
    }

    public function testCustomRegistration(): void
    {
        CalendarRegistry::register('my-jalali', static fn () => new JalaliCalendar(), ['kh']);

        self::assertTrue(CalendarRegistry::has('my-jalali'));
        self::assertSame(CalendarRegistry::get('my-jalali'), CalendarRegistry::get('kh'));
    }

    public function testDefaultCalendar(): void
    {
        self::assertSame('gregorian', CalendarRegistry::getDefaultCalendar()->getName());

        CalendarRegistry::setDefaultCalendar('jalali');

        self::assertSame('jalali', CalendarRegistry::getDefaultCalendar()->getName());
    }
}
