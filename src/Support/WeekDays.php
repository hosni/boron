<?php

declare(strict_types=1);

namespace Boron\Support;

/**
 * Week day names, indexed 0 = Monday ... 6 = Sunday
 * (a Julian Day Number modulo 7 gives exactly this index).
 */
final class WeekDays
{
    private const NAMES = [
        'en' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
        'fa' => ['دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه', 'شنبه', 'یکشنبه'],
        'ar' => ['الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت', 'الأحد'],
    ];

    public static function name(int $mondayBasedIndex, string $locale = 'en'): string
    {
        $names = self::NAMES[substr(strtolower($locale), 0, 2)] ?? self::NAMES['en'];

        return $names[(($mondayBasedIndex % 7) + 7) % 7];
    }

    public static function shortName(int $mondayBasedIndex, string $locale = 'en'): string
    {
        $name = self::name($mondayBasedIndex, $locale);

        return str_starts_with($locale, 'en') ? substr($name, 0, 3) : $name;
    }

    private function __construct()
    {
    }
}
