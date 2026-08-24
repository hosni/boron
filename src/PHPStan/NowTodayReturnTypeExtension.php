<?php

declare(strict_types=1);

namespace Boron\PHPStan;

use Boron\Carbon;
use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Type\DynamicFunctionReturnTypeExtension;
use PHPStan\Type\ObjectType;
use PHPStan\Type\Type;

/**
 * Laravel's now() / today() helpers return {@see Carbon} after Date::use().
 *
 * @codeCoverageIgnore Pure PHPStan wrapper.
 */
final class NowTodayReturnTypeExtension implements DynamicFunctionReturnTypeExtension
{
    public function isFunctionSupported(FunctionReflection $functionReflection): bool
    {
        return 'now' === $functionReflection->getName()
            || 'today' === $functionReflection->getName();
    }

    public function getTypeFromFunctionCall(
        FunctionReflection $functionReflection,
        FuncCall $functionCall,
        Scope $scope,
    ): Type {
        return new ObjectType(Carbon::class);
    }
}
