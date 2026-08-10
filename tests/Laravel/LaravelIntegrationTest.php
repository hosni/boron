<?php

declare(strict_types=1);

namespace Boron\Tests\Laravel;

use Boron\BoronInterface;
use Boron\Carbon;
use Boron\CarbonImmutable;
use Boron\Laravel\BoronServiceProvider;
use Carbon\Carbon as BaseCarbon;

// Testbench (and therefore Laravel) requires PHP 8.2+, while Boron itself
// supports 8.1; skip declaring the suite when testbench is not installed.
if (!class_exists(\Orchestra\Testbench\TestCase::class)) {
    return;
}

/**
 * Full Laravel integration tests running on a real (Testbench) application
 * with the BoronServiceProvider registered, exactly as it would be through
 * package auto-discovery.
 */
final class LaravelIntegrationTest extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app): array
    {
        return [BoronServiceProvider::class];
    }

    protected function tearDown(): void
    {
        Carbon::setDefaultCalendar('gregorian');
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function testAutoDiscoveryManifestPointsToTheProvider(): void
    {
        $composer = json_decode(
            (string) file_get_contents(__DIR__.'/../../composer.json'),
            true,
        );

        self::assertSame(
            [BoronServiceProvider::class],
            $composer['extra']['laravel']['providers'],
        );
    }

    public function testDateFacadeReturnsBoron(): void
    {
        $now = \Illuminate\Support\Facades\Date::now();

        self::assertInstanceOf(Carbon::class, $now);
        self::assertInstanceOf(BaseCarbon::class, $now);
        self::assertInstanceOf(BoronInterface::class, $now);

        self::assertInstanceOf(
            Carbon::class,
            \Illuminate\Support\Facades\Date::parse('2024-03-20'),
        );
        self::assertInstanceOf(
            Carbon::class,
            \Illuminate\Support\Facades\Date::create(2024, 3, 20),
        );
    }

    public function testGlobalHelpersReturnBoron(): void
    {
        self::assertInstanceOf(Carbon::class, now());
        self::assertInstanceOf(Carbon::class, today());

        self::assertSame('1403-01-01', (string) now()->setDate(2024, 3, 20)->toJalali());
    }

    public function testTestNowWorksThroughLaravel(): void
    {
        Carbon::setTestNow('2024-03-20 12:00:00');

        self::assertSame('1403-01-01', now()->toCalendarDateString('jalali'));
        self::assertSame('2024-03-20 12:00:00', now()->toDateTimeString());
    }

    public function testEloquentDatetimeCastsReturnBoron(): void
    {
        $model = new class extends \Illuminate\Database\Eloquent\Model {
            protected $guarded = [];
            protected $casts = ['published_at' => 'datetime'];
        };

        $model->setRawAttributes([
            'published_at' => '2024-03-20 08:30:00',
            'created_at' => '2025-03-21 00:00:00',
        ]);

        $publishedAt = $model->published_at;

        self::assertInstanceOf(Carbon::class, $publishedAt);
        self::assertSame('1403-01-01', (string) $publishedAt->toJalali());

        // Laravel's automatic created_at/updated_at casting too.
        self::assertInstanceOf(Carbon::class, $model->created_at);
        self::assertSame('1404-01-01', (string) $model->created_at->toJalali());
    }

    public function testEloquentImmutableCastReturnsBoron(): void
    {
        $model = new class extends \Illuminate\Database\Eloquent\Model {
            protected $guarded = [];
            protected $casts = ['published_at' => 'immutable_datetime'];
        };

        $model->setRawAttributes(['published_at' => '2024-03-20 08:30:00']);

        $publishedAt = $model->published_at;

        self::assertInstanceOf(CarbonImmutable::class, $publishedAt);
        self::assertInstanceOf(BoronInterface::class, $publishedAt);
        self::assertSame('1403-01-01', (string) $publishedAt->toJalali());
    }

    public function testDefaultCalendarAppliesAcrossTheApp(): void
    {
        Carbon::setDefaultCalendar('jalali');
        Carbon::setTestNow('2024-03-20 12:00:00');

        self::assertSame('1403-01-01', now()->toCalendarDateString());
        self::assertSame(1403, now()->calendarYear);
        self::assertSame('Farvardin', now()->calendarMonthName);
    }

    public function testAboutCommandShowsBoron(): void
    {
        \Illuminate\Support\Facades\Artisan::call('about', ['--json' => true]);

        $about = json_decode(\Illuminate\Support\Facades\Artisan::output(), true);

        self::assertArrayHasKey('boron', $about);

        $section = $about['boron'];

        self::assertSame(\Boron\Carbon::class, $section['date_class']);
        self::assertSame('gregorian', $section['default_calendar']);
        self::assertSame('en', $section['calendar_locale']);
        self::assertStringContainsString('jalali', $section['calendars']);
        self::assertStringContainsString('hijri', $section['calendars']);
        self::assertNotSame('unknown', $section['version']);
    }

    public function testModelSerializationKeepsStandardFormat(): void
    {
        // Boron must not change how Laravel serializes dates.
        $model = new class extends \Illuminate\Database\Eloquent\Model {
            protected $guarded = [];
            protected $casts = ['published_at' => 'datetime'];
        };

        $model->setRawAttributes(['published_at' => '2024-03-20 08:30:00']);

        self::assertSame(
            '2024-03-20T08:30:00.000000Z',
            $model->toArray()['published_at'],
        );
    }
}
