# Boron

**A multi-calendar, drop-in replacement for [Carbon](https://carbon.nesbot.com/).**

> Boron (B) is element **5** of the periodic table. Carbon (C) is element **6**.
> Boron sits right next to Carbon - a little lighter, and it knows more calendars.

Boron adds Jalali (Shamsi / Solar Hijri), Hijri (Islamic / Lunar), and Gregorian
calendars on top of Carbon. You keep the entire Carbon API - diffing,
localization, `setTestNow()`, type-hints against `Carbon\Carbon` - and gain a
calendar layer that converts freely between systems.

```php
use Boron\Carbon;

Carbon::parse('2024-03-20')->toJalali();          // 1403-01-01 (Nowruz!)
Carbon::parse('2024-03-20')->toHijri();           // 1445-09-10 (Ramadan 10)
Carbon::fromJalali(1403, 5, 19)->toDateString();  // 2024-08-09

Carbon::now()->calendarFormat('l j F Y', 'jalali', 'fa', true);
// چهارشنبه ۱ فروردین ۱۴۰۳
```

## Why Boron?

- **Not a reinvention** - `Boron\Carbon` extends `Carbon\Carbon`; Laravel and
  every Carbon type-hint keep working.
- **Two driver families** - pure-PHP arithmetic (inspired by
  [date-object](https://github.com/shahabyazdi/date-object)) and ICU via
  `ext-intl`.
- **Presentation-layer calendars** - the stored instant stays Gregorian; other
  calendars are a view for input and display.
- **Laravel-ready** - auto-discovered provider, Eloquent casts, `php artisan about`,
  a [PHPStan extension](laravel/phpstan.md) for `Date::*` / `now()` / Eloquent, and
  [Laravel Boost](laravel/boost.md) AI skills.

## Class family

| Class | Extends | Role |
|---|---|---|
| [`Boron\Carbon`](api/carbon.md) | `Carbon\Carbon` | Mutable drop-in |
| [`Boron\CarbonImmutable`](api/carbon-immutable.md) | `Carbon\CarbonImmutable` | Immutable drop-in |
| [`Boron\CarbonInterface`](api/carbon-interface.md) | `Carbon\CarbonInterface` | Calendar-aware contract |

## Languages

This site is available in **English** and **فارسی** — use the language switcher
in the header. On Read the Docs the locales live at `/en/…` and `/fa/…`
(separate RTD translation projects), not nested under `/en/…/fa/…`.

## Next steps

1. [Install](installation.md) the package
2. Read the [quick start](quick-start.md)
3. Understand the [mental model](mental-model.md)
4. Wire it into [Laravel](laravel/integration.md) if you use it
