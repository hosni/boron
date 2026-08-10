# Active calendar

Each instance can carry an active calendar used by calendar-aware members. It
does **not** change Carbon getters or formatters.

## Per instance

```php
use Boron\Carbon;

$date = Carbon::parse('2024-03-20')->withCalendar('jalali');

$date->getCalendar()->getName(); // jalali
$date->calendarYear;             // 1403
$date->calendarMonth;            // 1
$date->calendarDay;              // 1
$date->calendarMonthName;        // Farvardin
$date->calendarDaysInMonth;      // 31
$date->calendarDayOfYear;        // 1
$date->calendarName;             // jalali
$date->julianDay;                // 2460390
$date->calendarDate;             // CalendarDate instance

$date->toCalendarDateString();      // 1403-01-01
$date->toCalendarDateTimeString();  // 1403-01-01 00:00:00

// Carbon getters stay Gregorian
$date->year;                 // 2024
$date->format('Y-m-d');      // 2024-03-20
```

`withCalendar(null)` clears the override so the instance follows the global
default again.

## App-wide default

Shared by `Boron\Carbon` and `Boron\CarbonImmutable`:

```php
Carbon::setDefaultCalendar('jalali');
Carbon::setDefaultCalendarLocale('fa');

Carbon::now()->toCalendarDateString(); // e.g. 1405-05-19
```

In Laravel, set this once in a service provider `boot()` method.

## Magic properties

| Property | Meaning |
|---|---|
| `calendarYear` | Year in the active calendar |
| `calendarMonth` | Month (1-based) |
| `calendarDay` | Day of month |
| `calendarMonthName` | Localized month name (default locale) |
| `calendarDaysInMonth` | Length of the current calendar month |
| `calendarDayOfYear` | 1-based day of year |
| `calendarName` | Active calendar name |
| `calendarDate` | `CalendarDate` for the active calendar |
| `julianDay` | Civil Julian Day Number |
