<?php

declare(strict_types=1);

namespace Boron\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Guards the Laravel Boost discovery contract: Boost auto-loads
 * resources/boost/guidelines/core.blade.php and installs every
 * resources/boost/skills/{skill-name}/SKILL.md when users run
 * `php artisan boost:install`.
 */
final class BoostAssetsTest extends TestCase
{
    private const ROOT = __DIR__.'/..';

    public function testGuidelinesFileExistsAtTheDiscoveryPath(): void
    {
        $path = self::ROOT.'/resources/boost/guidelines/core.blade.php';

        self::assertFileExists($path);

        $content = (string) file_get_contents($path);

        self::assertStringContainsString('Boron', $content);
        // Boost renders guidelines through Blade; make sure no stray Blade
        // echo tags sneak in (code samples must stay literal).
        self::assertStringNotContainsString('{{', $content);
        self::assertStringNotContainsString('@php', $content);
    }

    public function testSkillsFollowTheAgentSkillsFormat(): void
    {
        $skillDirectories = glob(self::ROOT.'/resources/boost/skills/*', GLOB_ONLYDIR);

        self::assertNotEmpty($skillDirectories);

        foreach ($skillDirectories as $directory) {
            $skillFile = $directory.'/SKILL.md';

            self::assertFileExists($skillFile);

            $content = (string) file_get_contents($skillFile);

            self::assertMatchesRegularExpression(
                '/^---\nname: (\S+)\ndescription: .{20,}\n---\n/s',
                $content,
                "$skillFile must start with YAML frontmatter containing name and description",
            );

            preg_match('/^---\nname: (\S+)\n/', $content, $matches);

            self::assertSame(
                basename($directory),
                $matches[1],
                'Skill name in frontmatter must match its directory name',
            );
        }
    }
}
