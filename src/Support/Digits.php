<?php

declare(strict_types=1);

namespace Boron\Support;

/**
 * Helpers to convert between Latin, Persian and Arabic-Indic digits.
 */
final class Digits
{
    public const LATIN = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];
    public const PERSIAN = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    public const ARABIC = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];

    /**
     * Convert any Persian / Arabic-Indic digits in the string to Latin digits.
     */
    public static function toLatin(string $value): string
    {
        $value = str_replace(self::PERSIAN, self::LATIN, $value);

        return str_replace(self::ARABIC, self::LATIN, $value);
    }

    /**
     * Convert Latin digits to the digits commonly used with the locale
     * ("fa" => Persian digits, "ar" => Arabic-Indic digits).
     */
    public static function localize(string $value, string $locale): string
    {
        return match (substr(strtolower($locale), 0, 2)) {
            'fa' => str_replace(self::LATIN, self::PERSIAN, $value),
            'ar' => str_replace(self::LATIN, self::ARABIC, $value),
            default => $value,
        };
    }

    private function __construct()
    {
    }
}
