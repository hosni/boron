# Calendars

Boron ships arithmetic (pure PHP) and ICU (`ext-intl`) drivers. Register custom
ones through [`CalendarRegistry`](../api/calendar-registry.md).

## Built-in drivers

| Name | Aliases | Driver | Notes |
|---|---|---|---|
| `gregorian` | `miladi` | arithmetic | Proleptic Gregorian |
| `jalali` | `persian`, `shamsi` | arithmetic | 33-year cycle, aligned with ICU |
| `jalali-astronomical` | `persian-astronomical` | arithmetic | date-object astronomical table |
| `hijri` | `islamic`, `arabic` | arithmetic | Tabular (civil) Islamic |
| `jalali-intl` | - | ICU | Requires `ext-intl` |
| `hijri-intl` | - | ICU | ICU Islamic civil |
| `hijri-umalqura` | - | ICU | Saudi Umm al-Qura |
| `hijri-astronomical` | - | ICU | ICU `islamic` |
| `gregorian-intl` | - | ICU | ICU Gregorian (forced proleptic) |

```php
use Boron\CalendarRegistry;

CalendarRegistry::names();          // list of registered names
CalendarRegistry::has('jalali');    // true
CalendarRegistry::get('shamsi');    // same driver as jalali
```

## Choosing a driver

- **`jalali`** - default Jalali for apps; matches ICU day-for-day in the modern range.
- **`jalali-astronomical`** - faithful port of date-object's astronomical table; can
  differ by a day around a few historical leap years (1308/1309, 1341/1342, 1473/1474).
- **`hijri`** - civil tabular calendar; may differ ±1 day from moon-sighting calendars.
- **`hijri-umalqura`** - use for Saudi-official / religious business rules (needs intl).

## Supported range

Year **1** of each calendar and later. Gregorian dates before 622 AD cannot be
expressed in Jalali/Hijri and throw
[`UnsupportedCalendarRangeException`](exceptions.md).

## How conversion works

All calendars speak **Julian Day Number** (civil JDN). Converting A → B is:

1. `A.toJulianDayNumber(y, m, d)`
2. `B.fromJulianDayNumber(jdn)`

That is the same bridge used by ICU and by date-object.
