---
name: boron-development
description: Work with dates in multiple calendars (Jalali/Shamsi, Hijri, Gregorian) using Boron, the multi-calendar Carbon replacement. Covers creating, converting, parsing, formatting, and calendar-aware arithmetic.
---

# Boron Development

## When to use this skill

Use this skill whenever the task involves Jalali (Shamsi/Persian), Hijri (Islamic), or multi-calendar dates in an application using `boron/carbon`: displaying dates to users, parsing user-entered dates, storing them, date arithmetic in a non-Gregorian calendar, or localized date formatting.

## Core model - read this first

- Boron instances ARE Carbon: `Boron\Carbon extends Carbon\Carbon` and `Boron\CarbonImmutable extends Carbon\CarbonImmutable`. In Laravel, `now()`, `today()`, `Date::*` and Eloquent datetime casts already return them. Every Carbon method works unchanged.
- The stored instant is always Gregorian/UTC-based. Other calendars are a **view** on top. Store dates normally (Gregorian columns, standard casts); convert only at the presentation/input boundary.
- `->year`, `->month`, `->format('Y-m-d')` are Gregorian. Calendar-aware equivalents: `->calendarYear`, `->calendarMonth`, `->calendarFormat(...)`.
- Never write manual conversion math or use string hacks. Boron covers conversion, parsing, formatting, and arithmetic.

## Calendars

| Name | Aliases | Notes |
|---|---|---|
| `gregorian` | `miladi` | proleptic Gregorian |
| `jalali` | `persian`, `shamsi` | default Jalali driver; matches ICU/reality day-for-day |
| `jalali-astronomical` | `persian-astronomical` | date-object's astronomical approximation |
| `hijri` | `islamic`, `arabic` | tabular Islamic; can differ ±1 day from sighting-based calendars |
| `jalali-intl`, `hijri-intl`, `hijri-umalqura` | - | ICU drivers, require ext-intl; use `hijri-umalqura` for the Saudi official calendar |

## Creating dates from calendar input

```php
use Boron\Carbon;

Carbon::fromJalali(1403, 1, 1);                      // 2024-03-20 00:00
Carbon::fromHijri(1445, 9, 10, 20, 30);              // with time
Carbon::fromCalendar('hijri-umalqura', 1447, 1, 1, timezone: 'Asia/Tehran');

// Parsing user input: accepts Y/m/d, Y-m-d, Y.m.d, optional H:i[:s],
// and Persian (۱۴۰۳) or Arabic-Indic digits.
Carbon::parseFromCalendar('jalali', '1403/01/01 14:30');
Carbon::parseFromCalendar('jalali', '۱۴۰۳/۰۱/۰۱');
// Throws Boron\Exceptions\InvalidFormatException on bad input - catch it in form validation.
```

## Converting for display

```php
$date = now();

$jalali = $date->toJalali();          // Boron\CalendarDate value object
$jalali->year;                        // 1403
$jalali->getMonthName('fa');          // فروردین
(string) $jalali;                     // "1403-01-01"
$date->toHijri();                     // hijri CalendarDate
$date->toCalendarDate('hijri-umalqura');

// Formatting with PHP date() tokens; date tokens use the calendar,
// time/timezone tokens delegate to Carbon:
$date->calendarFormat('Y/m/d');                                  // 1403/01/01
$date->calendarFormat('l j F Y H:i', 'jalali', 'fa', true);      // چهارشنبه ۱ فروردین ۱۴۰۳ ۱۴:۰۵
```

## Active calendar (per instance or app-wide)

```php
// App-wide (e.g. in AppServiceProvider::boot for a Persian app):
\Boron\Carbon::setDefaultCalendar('jalali');
\Boron\Carbon::setDefaultCalendarLocale('fa');

now()->toCalendarDateString();   // "1405-05-19" - uses default calendar
now()->calendarYear;             // 1405
now()->calendarMonthName;        // مرداد

// Per instance:
$date = now()->withCalendar('hijri');
$date->calendarYear;             // hijri year; Carbon getters still Gregorian
```

## Calendar-aware arithmetic and boundaries

Plain Carbon `addMonth()`/`startOfMonth()` operate on Gregorian months. For the active calendar use:

```php
$date->addCalendarMonths(1);     // clamps day: Jalali 1403-06-31 + 1 month = 1403-07-30
$date->addCalendarYears(1);      // clamps: Esfand 30 (leap) + 1 year = Esfand 29
$date->startOfCalendarMonth();   // 00:00:00 of day 1 of the calendar month
$date->endOfCalendarMonth();     // 23:59:59 of the last day
$date->startOfCalendarYear();    // e.g. Farvardin 1 / Muharram 1
$date->endOfCalendarYear();
$date->setCalendarDate(1404, 1, 1);   // keeps time and timezone
$date->isCalendarLeapYear();
```

Day/hour/minute arithmetic is calendar-independent - keep using Carbon's `addDays()`, `diffInDays()`, `diffForHumans()`, etc.

## Typical Laravel patterns

```php
// Blade output:
{{ $post->published_at->calendarFormat('j F Y', 'jalali', 'fa', true) }}

// Form request: validate then parse Jalali input into a model attribute:
$post->published_at = \Boron\Carbon::parseFromCalendar('jalali', $request->input('published_at'));

// Query by Jalali month boundaries:
$from = \Boron\Carbon::fromJalali(1403, 6, 1)->startOfDay();
$to = $from->copy()->endOfCalendarMonth();
Post::whereBetween('published_at', [$from, $to])->get();
```

## Pitfalls

- Do not mix up `->year` (Gregorian) and `->calendarYear` (active calendar).
- `toImmutable()`/`toMutable()` return `Boron\CarbonImmutable`/`Boron\Carbon` and preserve the active calendar - never plain Carbon.
- `Illuminate\Support\Carbon::parse()->toJalali()` fails at runtime: `Date::use()` does not replace `Support\Carbon::parse()`. Use `Date::parse()`, `now()`, Eloquent casts, or `Boron\Carbon`. Include `vendor/boron/carbon/extension.neon` in PHPStan (automatic with `phpstan/extension-installer`) so those call sites type-check.
- Dates before year 1 of a calendar (Gregorian dates before 622 AD for Jalali/Hijri) throw `Boron\Exceptions\UnsupportedCalendarRangeException`.
- Tabular `hijri` is a civil approximation. For Saudi-official dates (religious events, KSA business rules) use `hijri-umalqura` (requires ext-intl).
- Serialize/store dates in Gregorian ISO format (Laravel's default); only convert at the UI boundary.
- Digits: use the `$localizeDigits` argument of `calendarFormat()` (or `Boron\Support\Digits::localize()`) for ۱۴۰۳-style output; never str_replace digits manually.
