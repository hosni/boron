<?php

declare(strict_types=1);

namespace Boron\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Guards the PHPStan extension discovery contract: phpstan/extension-installer
 * reads extra.phpstan.includes and loads extension.neon.
 */
final class PhpStanExtensionManifestTest extends TestCase
{
    public function testComposerRegistersThePhpstanExtension(): void
    {
        $composer = json_decode(
            (string) file_get_contents(__DIR__.'/../composer.json'),
            true,
        );

        self::assertSame(
            ['extension.neon'],
            $composer['extra']['phpstan']['includes'],
        );
        self::assertFileExists(__DIR__.'/../extension.neon');
        self::assertStringContainsString(
            'Boron\\PHPStan\\DateFacadeReturnTypeExtension',
            (string) file_get_contents(__DIR__.'/../extension.neon'),
        );
    }
}
