<?php

declare(strict_types=1);

namespace Boron\Laravel;

use Boron\CalendarRegistry;
use Boron\Carbon;
use Composer\InstalledVersions;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\ServiceProvider;

/**
 * Makes Laravel use Boron\Carbon everywhere it would use Carbon
 * (Date facade, Eloquent date casts, ...).
 *
 * Boron\Carbon is a true Carbon subclass, so every Carbon type-hint across
 * the framework and third-party packages stays satisfied.
 *
 * Auto-discovered; opt out with:
 *
 *     "extra": {"laravel": {"dont-discover": ["boron/carbon"]}}
 */
class BoronServiceProvider extends ServiceProvider
{
    private static function version(): string
    {
        if (InstalledVersions::isInstalled('boron/carbon')) {
            return InstalledVersions::getPrettyVersion('boron/carbon') ?? 'unknown';
        }

        // Running from the package itself (tests, path repository, ...).
        $root = InstalledVersions::getRootPackage();

        return 'boron/carbon' === $root['name'] ? ($root['pretty_version'] ?? 'dev') : 'unknown';
    }

    public function register(): void
    {
        Date::use(Carbon::class);

        $this->registerAbout();
    }

    /**
     * Show Boron in the `php artisan about` output.
     */
    protected function registerAbout(): void
    {
        if (!class_exists(AboutCommand::class)) {
            return;
        }

        AboutCommand::add('Boron', static fn () => [
            'Version' => self::version(),
            'Date Class' => Carbon::class,
            'Default Calendar' => CalendarRegistry::getDefaultCalendar()->getName(),
            'Calendar Locale' => CalendarRegistry::getDefaultLocale(),
            'Calendars' => AboutCommand::format(
                value: implode(', ', CalendarRegistry::names()),
                json: static fn () => CalendarRegistry::names(),
            ),
            'Intl (ICU) Drivers' => \extension_loaded('intl')
                ? \sprintf('ENABLED (ICU %s)', \defined('INTL_ICU_VERSION') ? INTL_ICU_VERSION : 'unknown')
                : 'DISABLED (ext-intl not loaded)',
            'Maintained by' => AboutCommand::format(
                value: 'Hossein Hosni',
                console: static fn (string $value) => "<fg=green>$value</>",
                json: static fn (string $value) => [
                    'name' => $value,
                    'email' => 'hosni.hossein@gmail.com',
                ],
            ),
        ]);
    }
}
