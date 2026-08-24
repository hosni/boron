<?php

declare(strict_types=1);

namespace Boron\Tests\PHPStan;

use PHPStan\Testing\TypeInferenceTestCase;
use PHPUnit\Framework\Attributes\DataProvider;

// PHPStan type inference needs illuminate/support (Date facade, now()/today()).
// Testbench — and therefore Laravel — requires PHP 8.2+; skip declaring the
// suite when illuminate is not installed (PHP 8.1 CI).
if (!class_exists(\Illuminate\Support\Facades\Date::class)) {
    return;
}

/**
 * @internal
 */
final class TypeInferenceTest extends TypeInferenceTestCase
{
    /**
     * @return iterable<mixed>
     */
    public static function dataFileAsserts(): iterable
    {
        yield from self::gatherAssertTypes(__DIR__.'/data/date.php');
    }

    /**
     * @param mixed ...$args
     */
    #[DataProvider('dataFileAsserts')]
    public function testFileAsserts(string $assertType, string $file, mixed ...$args): void
    {
        $this->assertFileAsserts($assertType, $file, ...$args);
    }

    public static function getAdditionalConfigFiles(): array
    {
        return [
            __DIR__.'/phpstan.neon',
        ];
    }
}
