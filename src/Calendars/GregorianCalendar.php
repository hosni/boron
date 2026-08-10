<?php

declare(strict_types=1);

namespace Boron\Calendars;

/**
 * Proleptic Gregorian calendar.
 */
class GregorianCalendar extends ArithmeticCalendar
{
    private const MONTH_NAMES = [
        'en' => [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December',
        ],
        'fa' => [
            'ژانویه', 'فوریه', 'مارس', 'آوریل', 'مه', 'ژوئن',
            'ژوئیه', 'اوت', 'سپتامبر', 'اکتبر', 'نوامبر', 'دسامبر',
        ],
        'ar' => [
            'يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو',
            'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر',
        ],
    ];

    public function getName(): string
    {
        return 'gregorian';
    }

    public function getMonthLengths(bool $leapYear): array
    {
        return [31, $leapYear ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
    }

    public function isLeapYear(int $year): bool
    {
        return (0 === $year % 4 && 0 !== $year % 100) || 0 === $year % 400;
    }

    public function getMonthNames(string $locale = 'en'): array
    {
        return self::MONTH_NAMES[$locale] ?? self::MONTH_NAMES['en'];
    }

    protected function commonYearLength(): int
    {
        return 365;
    }

    protected function epoch(): int
    {
        return 1_721_425;
    }

    protected function averageYearLength(): float
    {
        return 365.2425;
    }

    protected function leapYearsBefore(int $year): int
    {
        $y = $year - 1;

        return intdiv($y, 4) - intdiv($y, 100) + intdiv($y, 400);
    }
}
