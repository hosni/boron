<?php

declare(strict_types=1);

namespace Boron\PHPStan;

use Boron\CalendarDate;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\PropertiesClassReflectionExtension;
use PHPStan\Reflection\PropertyReflection;
use PHPStan\Type\IntegerType;
use PHPStan\Type\ObjectType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;

/**
 * Calendar magic properties on Illuminate\Support\Carbon and
 * Carbon\CarbonImmutable (Eloquent datetime / immutable_datetime).
 *
 * @codeCoverageIgnore Pure PHPStan wrapper.
 */
final class LaravelCarbonPropertiesExtension implements PropertiesClassReflectionExtension
{
    private static function typeOf(string $propertyName): Type
    {
        return match ($propertyName) {
            'calendarDate' => new ObjectType(CalendarDate::class),
            'calendarMonthName', 'calendarName' => new StringType(),
            default => new IntegerType(),
        };
    }

    public function hasProperty(ClassReflection $classReflection, string $propertyName): bool
    {
        if (!LaravelCarbonTargets::isDateInstance($classReflection)) {
            return false;
        }

        if ($classReflection->hasNativeProperty($propertyName)) {
            return false;
        }

        return \in_array($propertyName, LaravelCarbonTargets::CALENDAR_PROPERTIES, true);
    }

    public function getProperty(ClassReflection $classReflection, string $propertyName): PropertyReflection
    {
        return new CalendarPropertyReflection($classReflection, self::typeOf($propertyName));
    }
}
