<?php

declare(strict_types=1);

namespace Boron\PHPStan;

use Carbon\CarbonImmutable;
use PHPStan\Reflection\ClassReflection;

/**
 * Which class reflections the Laravel PHPStan layer should decorate.
 *
 * @internal
 *
 * @codeCoverageIgnore Pure PHPStan helper.
 */
final class LaravelCarbonTargets
{
    public const DATE_FACADE = 'Illuminate\Support\Facades\Date';

    public const SUPPORT_CARBON = 'Illuminate\Support\Carbon';

    /**
     * Calendar factories forwarded by Laravel's Date factory to the date class.
     *
     * @var list<string>
     */
    public const DATE_FACADE_CALENDAR_METHODS = [
        'fromCalendar',
        'fromJalali',
        'fromHijri',
        'parseFromCalendar',
        'setDefaultCalendar',
        'getDefaultCalendar',
        'setDefaultCalendarLocale',
        'getDefaultCalendarLocale',
    ];

    /**
     * Date facade factories that return a date instance (possibly nullable / false).
     *
     * @var list<string>
     */
    public const DATE_FACADE_INSTANCE_METHODS = [
        'create',
        'createFromDate',
        'createFromTime',
        'createFromTimeString',
        'createFromTimestamp',
        'createFromTimestampMs',
        'createFromTimestampUTC',
        'createMidnightDate',
        'fromSerialized',
        'getTestNow',
        'instance',
        'maxValue',
        'minValue',
        'now',
        'parse',
        'today',
        'tomorrow',
        'yesterday',
        'createFromFormat',
        'createSafe',
        'make',
        'fromCalendar',
        'fromJalali',
        'fromHijri',
        'parseFromCalendar',
    ];

    /**
     * @var list<string>
     */
    public const NULLABLE_INSTANCE_METHODS = [
        'getTestNow',
        'make',
    ];

    /**
     * @var list<string>
     */
    public const NULLABLE_OR_FALSE_INSTANCE_METHODS = [
        'createFromFormat',
        'createSafe',
    ];

    /**
     * Magic calendar properties from {@see \Boron\CarbonInterface}.
     *
     * @var list<string>
     */
    public const CALENDAR_PROPERTIES = [
        'calendarYear',
        'calendarMonth',
        'calendarDay',
        'calendarDate',
        'calendarMonthName',
        'calendarDaysInMonth',
        'calendarDayOfYear',
        'calendarName',
        'julianDay',
    ];

    public static function isDateFacade(ClassReflection $class): bool
    {
        return self::DATE_FACADE === $class->getName();
    }

    /**
     * Eloquent / leftover Laravel date types: Support\Carbon, CarbonImmutable,
     * and subclasses (skipping is done by the caller via hasNativeMethod).
     */
    public static function isDateInstance(ClassReflection $class): bool
    {
        $name = $class->getName();

        if (self::SUPPORT_CARBON === $name || CarbonImmutable::class === $name) {
            return true;
        }

        return $class->isSubclassOf(self::SUPPORT_CARBON)
            || $class->isSubclassOf(CarbonImmutable::class);
    }
}
