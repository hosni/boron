## Boron (boron/carbon)

This application uses Boron, a multi-calendar (Jalali/Shamsi, Hijri, Gregorian) drop-in replacement for Carbon. Its service provider calls `Date::use(\Boron\Carbon::class)`, so `now()`, `today()`, the `Date` facade, and Eloquent `datetime` casts all return `Boron\Carbon` instances (`immutable_datetime` casts return `Boron\CarbonImmutable`). Both are true Carbon subclasses - the entire Carbon API works unchanged.

- The underlying instant is always Gregorian; calendars are a *view*. Carbon getters (`->year`, `->format()`) stay Gregorian. Use the calendar API for other calendars.
- NEVER hand-roll date conversions (no manual Jalali/Hijri math, no string mangling). Use Boron's API:
  - `$date->toJalali()` / `$date->toHijri()` / `$date->toCalendarDate('hijri-umalqura')` - returns a `Boron\CalendarDate` value object (`->year`, `->month`, `->day`, `->format()`).
  - `Boron\Carbon::fromJalali(1403, 1, 1)` / `fromHijri(...)` / `fromCalendar($calendar, ...)` - create from calendar dates.
  - `Boron\Carbon::parseFromCalendar('jalali', '1403/01/01')` - parse user input (Persian/Arabic digits accepted).
  - `$date->calendarFormat('j F Y', 'jalali', 'fa', true)` - calendar-aware formatting with PHP date() tokens.
- Calendar-aware month/year arithmetic and boundaries: `addCalendarMonths()`, `startOfCalendarMonth()`, `endOfCalendarYear()`, etc. Plain `addMonth()` is Gregorian.
- Set an app-wide calendar with `\Boron\Carbon::setDefaultCalendar('jalali')` and locale with `setDefaultCalendarLocale('fa')`.
- Calendar names: `gregorian`, `jalali` (aliases: `persian`, `shamsi`), `hijri` (tabular; aliases: `islamic`, `arabic`), plus ICU drivers `jalali-intl`, `hijri-intl`, `hijri-umalqura` (require ext-intl). Tabular `hijri` can differ ±1 day from Umm al-Qura; use `hijri-umalqura` for the Saudi official calendar.
- PHPStan: include `vendor/boron/carbon/extension.neon` (automatic with `phpstan/extension-installer`) so `Date::*`, `now()`/`today()`, and Eloquent datetime attributes expose the calendar API. Do not call calendar methods on `Illuminate\Support\Carbon::parse()` — `Date::use()` does not replace that class. Use `Date::parse()`, `now()`, Eloquent casts, or `Boron\Carbon::parse()`.
