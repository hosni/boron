# Mental model

Keep these rules in mind and the API stays predictable.

## The instant is Gregorian

A `Boron\Carbon` instance is still a Gregorian datetime under the hood. Carbon
getters and formatters (`->year`, `->format('Y-m-d')`, `addMonth()`, …) behave
exactly as Carbon does.

Other calendars are a **view** used for:

- creating / parsing user input (`fromJalali`, `parseFromCalendar`)
- displaying (`toJalali`, `calendarFormat`)
- month/year arithmetic in that calendar (`addCalendarMonths`, …)

!!! important "Storage"
    Persist dates in Gregorian (Laravel's default ISO columns and casts). Convert
    only at the UI / input boundary. Do not store Jalali strings in the database
    unless you have a very specific reason.

## Active calendar vs Carbon getters

```php
$date = Carbon::parse('2024-03-20')->withCalendar('jalali');

$date->year;           // 2024  — Gregorian (Carbon)
$date->calendarYear;   // 1403  — Jalali (active calendar)
$date->format('Y-m-d');           // 2024-03-20
$date->toCalendarDateString();    // 1403-01-01
```

## Never hand-roll conversions

Do not write custom Jalali math, digit `str_replace` loops, or string splicing.
Boron already covers conversion, parsing (including Persian/Arabic-Indic digits),
formatting, and calendar arithmetic.

## Drop-in means drop-in

`Boron\Carbon instanceof Carbon\Carbon` is `true`. Pass it anywhere Carbon is
accepted. `toImmutable()` / `toMutable()` return Boron flavors and keep the
active calendar — they never leak plain Carbon.
