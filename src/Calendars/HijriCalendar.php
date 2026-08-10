<?php

declare(strict_types=1);

namespace Boron\Calendars;

/**
 * Tabular Islamic (Hijri / Lunar Hijri) calendar, ported from date-object's
 * "arabic" calendar.
 *
 * It uses the tabular leap-year pattern {2, 5, 7, 10, 13, 15, 18, 21, 24,
 * 26, 29} within each 30-year cycle and the civil (Friday) epoch. Tabular
 * dates can differ by a day or so from sighting-based calendars such as
 * Umm al-Qura; use the "hijri-umalqura" ICU driver when you need the Saudi
 * official calendar.
 *
 * @see https://en.wikipedia.org/wiki/Tabular_Islamic_calendar
 */
class HijriCalendar extends ArithmeticCalendar
{
    private const MONTH_NAMES = [
        'en' => [
            'Muharram', 'Safar', "Rabi' al-Awwal", "Rabi' al-Thani",
            'Jumada al-Awwal', 'Jumada al-Thani', 'Rajab', "Sha'ban",
            'Ramadan', 'Shawwal', "Dhu al-Qi'dah", 'Dhu al-Hijjah',
        ],
        'ar' => [
            'محرم', 'صفر', 'ربيع الأول', 'ربيع الآخر',
            'جمادى الأولى', 'جمادى الآخرة', 'رجب', 'شعبان',
            'رمضان', 'شوال', 'ذو القعدة', 'ذو الحجة',
        ],
        'fa' => [
            'محرم', 'صفر', 'ربیع‌الاول', 'ربیع‌الثانی',
            'جمادی‌الاول', 'جمادی‌الثانی', 'رجب', 'شعبان',
            'رمضان', 'شوال', 'ذی‌القعده', 'ذی‌الحجه',
        ],
    ];

    public function getName(): string
    {
        return 'hijri';
    }

    public function getMonthLengths(bool $leapYear): array
    {
        return [30, 29, 30, 29, 30, 29, 30, 29, 30, 29, 30, $leapYear ? 30 : 29];
    }

    public function isLeapYear(int $year): bool
    {
        return \in_array($year % 30, [2, 5, 7, 10, 13, 15, 18, 21, 24, 26, 29], true);
    }

    public function getMonthNames(string $locale = 'en'): array
    {
        return self::MONTH_NAMES[$locale] ?? self::MONTH_NAMES['en'];
    }

    protected function commonYearLength(): int
    {
        return 354;
    }

    protected function epoch(): int
    {
        return 1_948_439;
    }

    protected function averageYearLength(): float
    {
        return 354.36667;
    }

    protected function leapYearsBefore(int $year): int
    {
        // Exact integer form of round(11/30 * (year - 1)) used by
        // date-object; the float version truncates the wrong way for some
        // years (year % 30 == 16) which would make the day count
        // inconsistent with isLeapYear().
        return intdiv(11 * ($year - 1) + 15, 30);
    }
}
