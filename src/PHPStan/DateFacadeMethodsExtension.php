<?php

declare(strict_types=1);

namespace Boron\PHPStan;

use Boron\Carbon;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Reflection\ReflectionProvider;

/**
 * Expose Boron calendar factories on the Date facade (Date::fromJalali(),
 * Date::parseFromCalendar(), Date::setDefaultCalendar(), ...). Laravel's
 * DateFactory forwards unknown static calls to the configured date class.
 *
 * @codeCoverageIgnore Pure PHPStan wrapper.
 */
final class DateFacadeMethodsExtension implements MethodsClassReflectionExtension
{
    public function __construct(
        private ReflectionProvider $reflectionProvider,
    ) {
    }

    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        if (!LaravelCarbonTargets::isDateFacade($classReflection)) {
            return false;
        }

        if (!\in_array($methodName, LaravelCarbonTargets::DATE_FACADE_CALENDAR_METHODS, true)) {
            return false;
        }

        if ($classReflection->hasNativeMethod($methodName)) {
            return false;
        }

        return $this->reflectionProvider->hasClass(Carbon::class)
            && $this->reflectionProvider->getClass(Carbon::class)->hasNativeMethod($methodName);
    }

    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        return $this->reflectionProvider->getClass(Carbon::class)->getNativeMethod($methodName);
    }
}
