<?php

declare(strict_types=1);

namespace Boron\Tests;

use Boron\CalendarRegistry;
use PHPUnit\Framework\Attributes\RequiresPhpExtension;
use PHPUnit\Framework\TestCase;

/**
 * Cross-checks the pure-PHP arithmetic drivers against the ICU drivers.
 */
#[RequiresPhpExtension('intl')]
final class IcuParityTest extends TestCase
{
    public function testGregorianMatchesIcu(): void
    {
        $arithmetic = CalendarRegistry::gregorian();
        $icu = CalendarRegistry::get('gregorian-intl');

        foreach ([[1, 1, 1], [1000, 6, 15], [1582, 10, 15], [1970, 1, 1], [2000, 2, 29], [2026, 8, 10]] as $date) {
            self::assertSame(
                $arithmetic->toJulianDayNumber(...$date),
                $icu->toJulianDayNumber(...$date),
                'Mismatch for '.implode('-', $date),
            );
        }
    }

    public function testJalaliMatchesIcuPersianForEveryDayBetween1900And2100(): void
    {
        $jalali = CalendarRegistry::get('jalali');
        $icu = CalendarRegistry::get('jalali-intl');
        $gregorian = CalendarRegistry::gregorian();

        $start = $gregorian->toJulianDayNumber(1900, 1, 1);
        $end = $gregorian->toJulianDayNumber(2100, 12, 31);

        // Sampling every 3 days still crosses every month/year boundary
        // over two centuries while keeping the test fast.
        for ($julianDay = $start; $julianDay <= $end; $julianDay += 3) {
            self::assertSame(
                $icu->fromJulianDayNumber($julianDay),
                $jalali->fromJulianDayNumber($julianDay),
                "Jalali mismatch at JDN $julianDay",
            );
        }
    }

    public function testHijriTabularStaysWithinOneDayOfIcuIslamicCivil(): void
    {
        $hijri = CalendarRegistry::get('hijri');
        $icu = CalendarRegistry::get('hijri-intl');
        $gregorian = CalendarRegistry::gregorian();

        $start = $gregorian->toJulianDayNumber(1900, 1, 1);
        $end = $gregorian->toJulianDayNumber(2100, 12, 31);

        for ($julianDay = $start; $julianDay <= $end; $julianDay += 13) {
            [$year, $month, $day] = $hijri->fromJulianDayNumber($julianDay);

            $drift = abs($icu->toJulianDayNumber($year, $month, $day) - $julianDay);

            self::assertLessThanOrEqual(1, $drift, "Hijri drift > 1 day at JDN $julianDay");
        }
    }

    public function testIcuDriversRoundTrip(): void
    {
        $gregorian = CalendarRegistry::gregorian();
        $start = $gregorian->toJulianDayNumber(1950, 1, 1);
        $end = $gregorian->toJulianDayNumber(2077, 12, 31);

        foreach (['jalali-intl', 'hijri-intl', 'hijri-umalqura'] as $name) {
            $calendar = CalendarRegistry::get($name);

            for ($julianDay = $start; $julianDay <= $end; $julianDay += 41) {
                [$year, $month, $day] = $calendar->fromJulianDayNumber($julianDay);

                self::assertSame(
                    $julianDay,
                    $calendar->toJulianDayNumber($year, $month, $day),
                    "$name round trip failed at JDN $julianDay",
                );
            }
        }
    }

    public function testIcuMonthNames(): void
    {
        $names = CalendarRegistry::get('jalali-intl')->getMonthNames('fa');

        self::assertCount(12, $names);
        self::assertSame('فروردین', $names[0]);

        self::assertSame('Farvardin', CalendarRegistry::get('jalali-intl')->getMonthName(1, 'en'));
        self::assertSame('رمضان', CalendarRegistry::get('hijri-intl')->getMonthName(9, 'ar'));
    }
}
