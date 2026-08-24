<?php

declare(strict_types=1);

namespace Boron\PHPStan;

use Boron\CarbonInterface;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\Reflection\ReflectionProvider;

/**
 * Calendar methods on Illuminate\Support\Carbon and Carbon\CarbonImmutable
 * so Eloquent datetime attributes type-check ($model->created_at->toJalali()).
 *
 * Native methods win: {@see \Boron\CarbonImmutable} keeps its own signatures.
 *
 * @codeCoverageIgnore Pure PHPStan wrapper.
 */
final class LaravelCarbonMethodsExtension implements MethodsClassReflectionExtension
{
    public function __construct(
        private ReflectionProvider $reflectionProvider,
    ) {
    }

    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        if (!LaravelCarbonTargets::isDateInstance($classReflection)) {
            return false;
        }

        if ($classReflection->hasNativeMethod($methodName)) {
            return false;
        }

        if (!$this->reflectionProvider->hasClass(CarbonInterface::class)) {
            return false;
        }

        return $this->reflectionProvider
            ->getClass(CarbonInterface::class)
            ->hasNativeMethod($methodName);
    }

    public function getMethod(ClassReflection $classReflection, string $methodName): MethodReflection
    {
        return $this->reflectionProvider
            ->getClass(CarbonInterface::class)
            ->getNativeMethod($methodName);
    }
}
