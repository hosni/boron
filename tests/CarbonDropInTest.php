<?php

declare(strict_types=1);

namespace Boron\Tests;

use Carbon\Carbon as BaseCarbon;
use Carbon\CarbonImmutable as BaseCarbonImmutable;
use Carbon\CarbonInterface as BaseCarbonInterface;
use Boron\Carbon;
use Boron\CarbonImmutable;
use Boron\CarbonInterface;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the drop-in classes Boron\Carbon and
 * Boron\CarbonImmutable, which are true Carbon subclasses.
 */
final class CarbonDropInTest extends TestCase
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

    public function testIsTrueCarbonSubclass(): void
    {
        self::assertInstanceOf(BaseCarbon::class, Carbon::now());
        self::assertInstanceOf(BaseCarbonImmutable::class, CarbonImmutable::now());
        self::assertInstanceOf(CarbonInterface::class, Carbon::now());
        self::assertInstanceOf(BaseCarbonInterface::class, CarbonImmutable::now());

        // Can be passed anywhere a Carbon is expected.
        $acceptCarbon = static fn (BaseCarbon $date): string => $date->toDateString();
        self::assertSame('2024-03-20', $acceptCarbon(Carbon::parse('2024-03-20')));
    }

    public function testFluentApiKeepsBoronFamily(): void
    {
        $date = Carbon::parse('2024-03-20')->addDays(2)->subMonth()->startOfWeek();

        self::assertInstanceOf(Carbon::class, $date);

        self::assertInstanceOf(
            CarbonImmutable::class,
            CarbonImmutable::parse('2024-03-20')->addYear()->endOfDay(),
        );
    }

    public function testCalendarApi(): void
    {
        $date = Carbon::parse('2024-03-20 15:00', 'Asia/Tehran');

        self::assertSame('1403-01-01', (string) $date->toJalali());
        self::assertSame('1445-09-10', (string) $date->toHijri());
        self::assertSame('2024-03-20', Carbon::fromJalali(1403, 1, 1)->toDateString());
        self::assertSame(1403, $date->withCalendar('jalali')->calendarYear);
        self::assertSame(
            '1 Farvardin 1403',
            $date->calendarFormat('j F Y', 'jalali'),
        );
    }

    public function testConversionsNeverLeakPlainCarbon(): void
    {
        $immutable = Carbon::parse('2024-03-20')->withCalendar('jalali')->toImmutable();

        self::assertInstanceOf(CarbonImmutable::class, $immutable);
        self::assertNotSame(BaseCarbonImmutable::class, $immutable::class);
        self::assertSame('jalali', $immutable->getCalendar()->getName());

        $mutable = $immutable->toMutable();

        self::assertInstanceOf(Carbon::class, $mutable);
        self::assertNotSame(BaseCarbon::class, $mutable::class);
        self::assertSame('jalali', $mutable->getCalendar()->getName());
    }

    public function testImmutabilityAndCalendarSurviveModifications(): void
    {
        $date = CarbonImmutable::parse('2024-03-20')->withCalendar('jalali');
        $next = $date->addDay();

        self::assertNotSame($date, $next);
        self::assertSame('1403-01-01', $date->toCalendarDateString());
        self::assertSame('1403-01-02', $next->toCalendarDateString());
        self::assertSame('jalali', $next->getCalendar()->getName());
    }

    public function testSerializationKeepsCalendar(): void
    {
        $date = Carbon::parse('2024-03-20 10:00:00', 'Asia/Tehran')->withCalendar('jalali');
        $copy = unserialize(serialize($date));

        self::assertTrue($copy->equalTo($date));
        self::assertSame('jalali', $copy->getCalendar()->getName());
    }

    public function testDefaultCalendarIsSharedAcrossTheWholeFamily(): void
    {
        Carbon::setDefaultCalendar('jalali');

        self::assertSame('jalali', Carbon::now()->getCalendar()->getName());
        self::assertSame('jalali', CarbonImmutable::now()->getCalendar()->getName());
    }

    public function testCalendarArithmetic(): void
    {
        self::assertSame(
            '1403-07-30',
            Carbon::fromJalali(1403, 6, 31)->addCalendarMonths(1)->toCalendarDateString(),
        );
        self::assertSame(
            '1403-12-30',
            CarbonImmutable::fromJalali(1403, 5, 19)->endOfCalendarYear()->toCalendarDateString(),
        );
    }
}
