# Boron

**A multi-calendar, drop-in replacement for [Carbon](https://carbon.nesbot.com/).**

> Boron (B) is element **5** of the periodic table. Carbon (C) is element **6**.
> Boron sits right next to Carbon — a little lighter, and it knows more calendars. :)

Boron adds a **multi-calendar system** on top of Carbon: Jalali (Shamsi /
Solar Hijri), Hijri (Islamic / Lunar) and Gregorian, freely convertible to
each other. You still get the entire Carbon feature set — diffing,
localization, `setTestNow()`, `CarbonInterface`, everything.

The calendar engine is a PHP port of the Julian-Day-Number design of
[shahabyazdi/date-object](https://github.com/shahabyazdi/date-object), and every
calendar is also available through a second driver backed by PHP's `intl`
extension (ICU).

```php
use Boron\Carbon;

Carbon::parse('2024-03-20')->toJalali();          // 1403-01-01 (Nowruz!)
Carbon::parse('2024-03-20')->toHijri();           // 1445-09-10 (Ramadan 10)
Carbon::fromJalali(1403, 5, 19)->toDateString();  // 2024-08-09

Carbon::now()->calendarFormat('l j F Y', 'jalali', 'fa', true);
// چهارشنبه ۱ فروردین ۱۴۰۳
```

## The class family

Boron does **not** reinvent Carbon. It extends it:

| Class | Extends | Use it when... |
|---|---|---|
| `Boron\Carbon` | `Carbon\Carbon` | mutable dates — Laravel's `Date::use()`, Eloquent casts, anything type-hinted against `Carbon\Carbon` |
| `Boron\CarbonImmutable` | `Carbon\CarbonImmutable` | immutable dates |

Both implement `Boron\CarbonInterface`, which **extends
`Carbon\CarbonInterface`** and adds the calendar API.

`toImmutable()` / `toMutable()` stay inside the Boron family and never leak
plain Carbon instances; the active calendar travels along.

## Installation

```bash
composer require hosni/boron
```

Requirements: PHP 8.1+, `nesbot/carbon` ^3.0. The `intl` extension is
optional — it unlocks the ICU drivers (`*-intl`, `hijri-umalqura`).

## Laravel

**Supported: Laravel 11, 12 and 13** — Boron requires Carbon 3, which Laravel
supports since `v11.0.0` (`nesbot/carbon: ^2.72.2|^3.0`; Laravel 12+ requires
Carbon 3 exclusively). Laravel 10 and below pin Carbon 2 and cannot be used
with Boron.

The service provider is auto-discovered and calls
`Date::use(\Boron\Carbon::class)`, so `now()`, `today()`, Eloquent date
casts, etc. all become calendar-aware:

```php
use Illuminate\Support\Facades\Date;

Date::now()->toJalali();                    // works, returns CalendarDate
now()->toCalendarDateString('jalali');      // helpers return Boron\Carbon
User::first()->created_at->calendarFormat('Y/m/d', 'jalali');
```

Eloquent `datetime` casts yield `Boron\Carbon` and `immutable_datetime`
casts yield `Boron\CarbonImmutable`, while JSON serialization of models
keeps Laravel's standard ISO format. All of this is covered by the
Testbench integration test suite (`tests/Laravel`).

Boron also registers itself with `php artisan about` (version, active
calendar drivers, ICU availability).

### Laravel Boost (AI agents)

Boron ships [Laravel Boost](https://laravel.com/docs/boost) assets, so AI
agents in Boost-enabled projects automatically know how to use it:

- **Guidelines** (`resources/boost/guidelines/core.blade.php`) — always-on
  context loaded into `CLAUDE.md`/`AGENTS.md` by `php artisan boost:install`.
- **Skill** (`resources/boost/skills/boron-development/SKILL.md`) — the
  `boron-development` agent skill with in-depth usage patterns and pitfalls.

## Calendars

| Name | Aliases | Driver | Notes |
|---|---|---|---|
| `gregorian` | `miladi` | arithmetic | proleptic Gregorian |
| `jalali` | `persian`, `shamsi` | arithmetic | 33-year cycle, aligned with ICU |
| `jalali-astronomical` | `persian-astronomical` | arithmetic | date-object astronomical table |
| `hijri` | `islamic`, `arabic` | arithmetic | tabular (civil) Islamic |
| `jalali-intl` | — | ICU | requires `ext-intl` |
| `hijri-intl` | — | ICU | Islamic civil (ICU) |
| `hijri-umalqura` | — | ICU | Saudi Umm al-Qura |
| `hijri-astronomical` | — | ICU | ICU `islamic` |
| `gregorian-intl` | — | ICU | ICU Gregorian (forced proleptic) |

Caveats:

- Arithmetic `jalali` matches ICU day-for-day in the modern range; the
  astronomical driver can diverge by a day around a few historical leap
  years (1308/1309, 1341/1342, 1473/1474).
- Tabular `hijri` dates are a *civil* approximation and may differ by ±1 day
  from sighting-based calendars; use `hijri-umalqura` for the Saudi calendar.
- Supported range: year 1 of each calendar and later (Gregorian dates before
  622 AD cannot be expressed in Jalali/Hijri and throw a range exception).

## Usage

### Converting

```php
use Boron\Carbon;

$date = Carbon::parse('2024-03-20 15:30', 'Asia/Tehran');

$date->toJalali();               // CalendarDate: 1403-01-01
$date->toHijri();                // CalendarDate: 1445-09-10
$date->toCalendarDate('hijri-umalqura');  // any registered calendar
$date->julianDayNumber();        // 2460390

// CalendarDate is a small immutable value object:
$jalali = $date->toJalali();
$jalali->year;                   // 1403
$jalali->month;                  // 1
$jalali->day;                    // 1
$jalali->isLeapYear();           // true
$jalali->getMonthName('fa');     // فروردین
$jalali->to('hijri');            // convert again
$jalali->format('l j F Y', 'fa', true); // چهارشنبه ۱ فروردین ۱۴۰۳
$jalali->toCarbon('Asia/Tehran'); // back to Boron\Carbon at midnight
```

### Creating

```php
Carbon::fromJalali(1403, 1, 1);                     // 2024-03-20 00:00
Carbon::fromHijri(1445, 9, 10, 20, 30);             // with time
Carbon::fromCalendar('hijri-umalqura', 1445, 9, 1); // any calendar
Carbon::parseFromCalendar('jalali', '1403/01/01 14:30');
Carbon::parseFromCalendar('jalali', '۱۴۰۳/۰۱/۰۱');  // Persian digits OK
```

### The active calendar

Each instance can carry an "active calendar" that calendar-aware members use
by default. It does **not** change any Carbon behavior:

```php
$date = Carbon::parse('2024-03-20')->withCalendar('jalali');

$date->calendarYear;         // 1403
$date->calendarMonth;        // 1
$date->calendarDay;          // 1
$date->calendarMonthName;    // Farvardin
$date->calendarDaysInMonth;  // 31
$date->calendarDayOfYear;    // 1
$date->toCalendarDateString();      // 1403-01-01
$date->toCalendarDateTimeString();  // 1403-01-01 00:00:00

$date->year;                 // 2024 — Carbon getters untouched
$date->format('Y-m-d');      // 2024-03-20 — Carbon format untouched
```

Or set it globally (shared by `Carbon` and `CarbonImmutable`):

```php
Carbon::setDefaultCalendar('jalali');
Carbon::setDefaultCalendarLocale('fa');

Carbon::now()->toCalendarDateString();  // 1405-05-19
```

### Formatting

`calendarFormat()` accepts PHP `date()` tokens. Date tokens
(`Y y m n d j t L z S F M l D N w`) are rendered in the calendar; everything
else (time, timezone, ...) is delegated to Carbon:

```php
$date = Carbon::parse('2024-03-20 14:05')->withCalendar('jalali');

$date->calendarFormat('Y/m/d');            // 1403/01/01
$date->calendarFormat('j F Y H:i');        // 1 Farvardin 1403 14:05
$date->calendarFormat('j F Y', 'hijri');   // 10 Ramadan 1445
$date->calendarFormat('l j F Y', locale: 'fa', localizeDigits: true);
// چهارشنبه ۱ فروردین ۱۴۰۳
```

### Calendar-aware arithmetic

Carbon's `addMonth()` etc. keep operating on the Gregorian calendar. For
month/year math in the active calendar:

```php
$date = Carbon::fromJalali(1403, 6, 31);       // Shahrivar 31

$date->addCalendarMonths(1);                  // 1403-07-30 (clamped, Mehr has 30 days)
Carbon::fromJalali(1403, 12, 30)->addCalendarYears(1);  // 1404-12-29 (clamped)

$date->startOfCalendarMonth();                // 1403-06-01 00:00:00
$date->endOfCalendarMonth();                  // 1403-06-31 23:59:59
$date->startOfCalendarYear();                 // 1403-01-01
$date->endOfCalendarYear();                   // 1403-12-30 (leap year!)

$date->setCalendarDate(1404, 1, 1);           // keeps time & timezone
$date->isCalendarLeapYear();                  // per active calendar
```

Day/hour/... arithmetic is calendar-independent — just use Carbon's own
`addDays()`, `addHours()`, `diffInDays()`, ...

### Immutability

```php
use Boron\Carbon;
use Boron\CarbonImmutable;

$date = CarbonImmutable::now()->withCalendar('jalali');
$next = $date->addDay();     // new instance, calendar preserved

Carbon::now()->toImmutable();            // Boron\CarbonImmutable
CarbonImmutable::now()->toMutable();     // Boron\Carbon, never plain Carbon
```

### Custom calendars

Implement `Boron\Calendars\CalendarInterface` (or reuse the ICU driver
for any ICU calendar keyword) and register it:

```php
use Boron\CalendarRegistry;
use Boron\Calendars\IcuCalendar;
use Boron\Carbon;

CalendarRegistry::register('buddhist', fn () => new IcuCalendar('buddhist'));

Carbon::now()->toCalendarDate('buddhist');
```

## Testing

```bash
composer test
```

The suite includes a parity test that checks the arithmetic Jalali driver
against ICU's Persian calendar for every day between 1900 and 2100, plus
round-trip tests for all drivers over 1800–2200, and a Laravel integration
suite running on [Orchestra Testbench](https://github.com/orchestral/testbench)
(Date facade, helpers, Eloquent casts, serialization).

## Credits

- [Hossein Hosni](https://github.com/hosni) — author of Boron.
- [Shahab Yazdi](https://github.com/shahabyazdi) — the calendar engine design
  and the Persian astronomical leap-year table come from his
  [date-object](https://github.com/shahabyazdi/date-object) library.
- [Brian Nesbitt, kylekatarnls & Carbon contributors](https://github.com/CarbonPHP/carbon)
  — the shoulders Boron stands on.
- [ICU](https://icu.unicode.org/) — the reference implementation behind the
  `intl` drivers.

## License

MIT
