<?php

declare(strict_types=1);

namespace Boron\PHPStan;

use Boron\Carbon;
use PhpParser\Node\Expr\StaticCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Type\Constant\ConstantBooleanType;
use PHPStan\Type\DynamicStaticMethodReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

/**
 * Date::parse() / now() / create() / fromJalali() / ... return {@see Carbon}
 * after {@see \Boron\Laravel\BoronServiceProvider} calls Date::use().
 *
 * Laravel's facade @method tags still say Illuminate\Support\Carbon; this
 * overrides that identity for PHPStan without stubbing the facade.
 *
 * @codeCoverageIgnore Pure PHPStan wrapper.
 */
final class DateFacadeReturnTypeExtension implements DynamicStaticMethodReturnTypeExtension
{
    public function getClass(): string
    {
        return LaravelCarbonTargets::DATE_FACADE;
    }

    public function isStaticMethodSupported(MethodReflection $methodReflection): bool
    {
        return \in_array($methodReflection->getName(), LaravelCarbonTargets::DATE_FACADE_INSTANCE_METHODS, true);
    }

    public function getTypeFromStaticMethodCall(
        MethodReflection $methodReflection,
        StaticCall $methodCall,
        Scope $scope,
    ): Type {
        $dateType = new ObjectType(Carbon::class);
        $name = $methodReflection->getName();

        if (\in_array($name, LaravelCarbonTargets::NULLABLE_INSTANCE_METHODS, true)) {
            return TypeCombinator::addNull($dateType);
        }

        if (\in_array($name, LaravelCarbonTargets::NULLABLE_OR_FALSE_INSTANCE_METHODS, true)) {
            return TypeCombinator::addNull(TypeCombinator::union($dateType, new ConstantBooleanType(false)));
        }

        return $dateType;
    }
}
