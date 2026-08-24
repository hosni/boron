# PHPStan

`Date::use(Boron\Carbon::class)` only changes **runtime**. PHPStan still reads
Laravel's facade `@method` tags, which say `Illuminate\Support\Carbon`, so
`Date::parse()->toJalali()` looks like a missing method.

Boron ships a PHPStan extension that:

1. Types `Date::*` factories and `now()` / `today()` as `Boron\Carbon`.
2. Exposes the calendar API (`toJalali()`, `calendarFormat()`, `calendarYear`,
   ...) on `Illuminate\Support\Carbon` and `Carbon\CarbonImmutable`, so Eloquent
   `$model->created_at->toJalali()` type-checks without fighting Larastan's
   datetime casts.
3. Adds calendar factories on the Date facade: `Date::fromJalali()`,
   `Date::fromHijri()`, `Date::fromCalendar()`, `Date::parseFromCalendar()`,
   plus `Date::setDefaultCalendar()` / `getDefaultCalendar()` /
   `setDefaultCalendarLocale()` / `getDefaultCalendarLocale()`.

## Install

If the project uses [`phpstan/extension-installer`](https://github.com/phpstan/extension-installer),
the extension is included automatically (`composer extra.phpstan.includes`).

Otherwise add one line to `phpstan.neon`:

```neon
includes:
    - vendor/boron/carbon/extension.neon
```

No stub files to maintain. Larastan is optional; the extension works with
plain PHPStan.

## What it does not change

`Date::use()` does **not** replace `Illuminate\Support\Carbon::parse()` or
`Carbon\Carbon::parse()`. Those still return a plain Carbon without calendar
methods and throw at runtime if you call `toJalali()`.

Use one of:

```php
Date::parse('2024-03-20')->toJalali();
now()->toJalali();
$user->created_at->toJalali();
\Boron\Carbon::parse('2024-03-20')->toJalali();
```

PHPStan will still accept `Illuminate\Support\Carbon::parse()->toJalali()`
because Eloquent attributes are typed as `Support\Carbon`. That call is a
runtime error — prefer `Date::` / `now()` / Eloquent / `Boron\Carbon`.

## Opting out

If you disable Boron's provider
(`extra.laravel.dont-discover: ["boron/carbon"]`), also ignore the PHPStan
extension so Date factories are not typed as `Boron\Carbon`:

```json
{
    "extra": {
        "phpstan/extension-installer": {
            "ignore": ["boron/carbon"]
        }
    }
}
```

Without the installer, simply omit `vendor/boron/carbon/extension.neon` from
`includes`.
