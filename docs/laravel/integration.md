# Laravel integration

## Supported versions

| Laravel | Carbon | Boron |
|---|---|---|
| 11 | Carbon 2 *or* 3 | ✅ (Carbon 3 required) |
| 12 | Carbon 3 | ✅ |
| 13 | Carbon 3 | ✅ |
| ≤ 10 | Carbon 2 | ❌ |

## What the provider does

`Boron\Laravel\BoronServiceProvider` is auto-discovered and:

1. Calls `Date::use(Boron\Carbon::class)` so `now()`, `today()`, the `Date`
   facade, and Eloquent `datetime` casts return `Boron\Carbon`
   (`immutable_datetime` → `Boron\CarbonImmutable`).
2. Registers a **Boron** section in `php artisan about` (version, date class,
   default calendar, locale, driver list, ICU status).

Opt out of discovery:

```json
"extra": {
    "laravel": {
        "dont-discover": ["boron/carbon"]
    }
}
```

## Typical usage

```php
use Boron\Carbon;
use Illuminate\Support\Facades\Date;

Date::now()->toJalali();
now()->toCalendarDateString('jalali');
$user->created_at->calendarFormat('Y/m/d', 'jalali', 'fa', true);

// Form input → model
$post->published_at = Carbon::parseFromCalendar('jalali', $request->input('published_at'));
```

## App-wide Jalali default

```php
// AppServiceProvider::boot()
\Boron\Carbon::setDefaultCalendar('jalali');
\Boron\Carbon::setDefaultCalendarLocale('fa');
```

## Eloquent

No special cast class is required. With the provider registered:

- `protected $casts = ['published_at' => 'datetime'];` → `Boron\Carbon`
- `'immutable_datetime'` → `Boron\CarbonImmutable`

JSON serialization stays Laravel's standard ISO format.

## Testing

The package ships an Orchestra Testbench suite under `tests/Laravel` covering
the Date facade, helpers, Eloquent casts, serialization, and the about command.
