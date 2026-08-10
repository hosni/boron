<?php

declare(strict_types=1);

namespace Boron\Calendars;

/**
 * Jalali (Solar Hijri / Shamsi) calendar using the arithmetic 33-year cycle
 * leap rule, ported from date-object's "jalali" calendar.
 *
 * This rule agrees with the astronomical calendar for all dates relevant to
 * everyday use (roughly 1178-1633 AP / 1799-2254 AD).
 */
class JalaliCalendar extends ArithmeticCalendar
{
    protected const MONTH_NAMES = [
        'en' => [
            'Farvardin', 'Ordibehesht', 'Khordad', 'Tir', 'Mordad', 'Shahrivar',
            'Mehr', 'Aban', 'Azar', 'Dey', 'Bahman', 'Esfand',
        ],
        'fa' => [
            'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور',
            'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند',
        ],
        'ar' => [
            'فروردین', 'أرديبهشت', 'خرداد', 'تير', 'مرداد', 'شهريور',
            'مهر', 'آبان', 'آذر', 'دي', 'بهمن', 'إسفند',
        ],
    ];

    /**
     * Number of integers k in [0, $n] with (k % 33) % 4 === 1.
     */
    private static function leapResiduesUpTo(int $n): int
    {
        $fullCycles = intdiv($n + 1, 33);
        $remainder = ($n + 1) % 33;

        return $fullCycles * 8 + intdiv(min($remainder, 30) + 2, 4);
    }

    public function getName(): string
    {
        return 'jalali';
    }

    public function getMonthLengths(bool $leapYear): array
    {
        return [31, 31, 31, 31, 31, 31, 30, 30, 30, 30, 30, $leapYear ? 30 : 29];
    }

    public function isLeapYear(int $year): bool
    {
        return (($year + 12) % 33) % 4 === 1;
    }

    public function getMonthNames(string $locale = 'en'): array
    {
        return static::MONTH_NAMES[$locale] ?? static::MONTH_NAMES['en'];
    }

    protected function commonYearLength(): int
    {
        return 365;
    }

    protected function epoch(): int
    {
        // Chosen so that (proleptic) 1/1/1 falls on JDN 1948320, exactly
        // like ICU's PersianCalendar; this anchors 1403-01-01 on 2024-03-20.
        return 1_948_319;
    }

    protected function averageYearLength(): float
    {
        return 365.2422;
    }

    protected function leapYearsBefore(int $year): int
    {
        // Count years y in [1, year - 1] with ((y + 12) % 33) % 4 === 1,
        // i.e. (y + 12) % 33 in {1, 5, 9, 13, 17, 21, 25, 29}.
        //
        // date-object uses Birashk's 2820-year formula here, but that
        // formula contradicts the 33-year leap rule for some years (most
        // notably it makes 1404 leap instead of 1403), so Boron counts with
        // the same rule used by isLeapYear() to stay self-consistent and
        // aligned with the real Iranian calendar (and with ICU).
        return self::leapResiduesUpTo($year + 11) - self::leapResiduesUpTo(12);
    }
}
