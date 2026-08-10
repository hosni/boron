<?php

declare(strict_types=1);

namespace Boron;

use Boron\Calendars\CalendarInterface;
use Boron\Calendars\GregorianCalendar;
use Boron\Calendars\HijriCalendar;
use Boron\Calendars\IcuCalendar;
use Boron\Calendars\JalaliCalendar;
use Boron\Calendars\PersianAstronomicalCalendar;
use Boron\Exceptions\UnknownCalendarException;

/**
 * Central registry of calendar drivers.
 *
 * Calendars are registered lazily (as factories) and resolved by name or
 * alias. You can register your own calendars:
 *
 *     CalendarRegistry::register('buddhist', fn () => new IcuCalendar('buddhist'));
 */
final class CalendarRegistry
{
    /** @var array<string, callable(): CalendarInterface> */
    private static array $factories = [];

    /** @var array<string, CalendarInterface> */
    private static array $instances = [];

    /** @var array<string, string> alias => canonical name */
    private static array $aliases = [];

    private static bool $bootstrapped = false;

    private static CalendarInterface|string $defaultCalendar = 'gregorian';

    private static string $defaultLocale = 'en';

    /**
     * Default calendar used by Boron instances that don't set one explicitly.
     */
    public static function setDefaultCalendar(string|CalendarInterface $calendar): void
    {
        if (\is_string($calendar)) {
            // Resolve eagerly so invalid names fail fast.
            $calendar = self::get($calendar);
        }

        self::$defaultCalendar = $calendar;
    }

    public static function getDefaultCalendar(): CalendarInterface
    {
        return self::resolve(self::$defaultCalendar);
    }

    /**
     * Default locale used for month names ("en", "fa", "ar", ...).
     */
    public static function setDefaultLocale(string $locale): void
    {
        self::$defaultLocale = $locale;
    }

    public static function getDefaultLocale(): string
    {
        return self::$defaultLocale;
    }

    /**
     * @param callable(): CalendarInterface|CalendarInterface $factory
     * @param list<string>                                    $aliases
     */
    public static function register(string $name, callable|CalendarInterface $factory, array $aliases = []): void
    {
        self::bootstrap();

        $name = self::normalize($name);

        if ($factory instanceof CalendarInterface) {
            self::$instances[$name] = $factory;
        } else {
            self::$factories[$name] = $factory;
            unset(self::$instances[$name]);
        }

        foreach ($aliases as $alias) {
            self::$aliases[self::normalize($alias)] = $name;
        }
    }

    public static function get(string $name): CalendarInterface
    {
        self::bootstrap();

        $key = self::normalize($name);
        $key = self::$aliases[$key] ?? $key;

        if (isset(self::$instances[$key])) {
            return self::$instances[$key];
        }

        if (!isset(self::$factories[$key])) {
            throw UnknownCalendarException::forName($name, self::names());
        }

        return self::$instances[$key] = (self::$factories[$key])();
    }

    public static function resolve(string|CalendarInterface $calendar): CalendarInterface
    {
        return $calendar instanceof CalendarInterface ? $calendar : self::get($calendar);
    }

    public static function has(string $name): bool
    {
        self::bootstrap();

        $key = self::normalize($name);
        $key = self::$aliases[$key] ?? $key;

        return isset(self::$instances[$key]) || isset(self::$factories[$key]);
    }

    /**
     * @return list<string> canonical names of the registered calendars
     */
    public static function names(): array
    {
        self::bootstrap();

        return array_values(array_unique([
            ...array_keys(self::$factories),
            ...array_keys(self::$instances),
        ]));
    }

    public static function gregorian(): GregorianCalendar
    {
        /* @var GregorianCalendar */
        return self::get('gregorian');
    }

    private static function normalize(string $name): string
    {
        return strtolower(trim($name));
    }

    private static function bootstrap(): void
    {
        if (self::$bootstrapped) {
            return;
        }

        self::$bootstrapped = true;

        self::register('gregorian', static fn () => new GregorianCalendar(), ['miladi', 'gregory']);

        self::register(
            'jalali',
            static fn () => new JalaliCalendar(),
            ['persian', 'shamsi', 'solar-hijri', 'jalaali'],
        );

        self::register(
            'jalali-astronomical',
            static fn () => new PersianAstronomicalCalendar(),
            ['persian-astronomical'],
        );

        self::register(
            'hijri',
            static fn () => new HijriCalendar(),
            ['islamic', 'arabic', 'lunar-hijri', 'ghamari', 'islamic-tabular'],
        );

        // ICU-backed drivers (require ext-intl; they fail lazily on resolve).
        self::register(
            'jalali-intl',
            static fn () => new IcuCalendar('persian', 'jalali-intl'),
            ['persian-intl', 'shamsi-intl'],
        );

        self::register(
            'hijri-intl',
            static fn () => new IcuCalendar('islamic-civil', 'hijri-intl'),
            ['islamic-civil'],
        );

        self::register(
            'hijri-umalqura',
            static fn () => new IcuCalendar('islamic-umalqura', 'hijri-umalqura'),
            ['islamic-umalqura', 'umalqura'],
        );

        self::register(
            'hijri-astronomical',
            static fn () => new IcuCalendar('islamic', 'hijri-astronomical'),
            ['islamic-astronomical'],
        );

        self::register(
            'gregorian-intl',
            static fn () => new IcuCalendar('gregorian', 'gregorian-intl'),
        );
    }

    private function __construct()
    {
    }
}
