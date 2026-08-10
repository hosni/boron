# Arithmetic

Carbon's `addMonth()` / `startOfMonth()` operate on **Gregorian** months. For
the active calendar, use the calendar-aware methods.

## Month and year math

Days are **clamped** to the last valid day of the target month:

```php
use Boron\Carbon;

$date = Carbon::fromJalali(1403, 6, 31); // Shahrivar 31

$date->addCalendarMonths(1);  // 1403-07-30  (Mehr has 30 days)
$date->addCalendarMonths(7);  // crosses into 1404

Carbon::fromJalali(1403, 12, 30)->addCalendarYears(1);
// 1404-12-29  (Esfand 30 in leap 1403 → Esfand 29 in 1404)
```

### Methods

| Method | Effect |
|---|---|
| `addCalendarMonths($n)` / `subCalendarMonths($n)` | ± months with clamping |
| `addCalendarMonth()` / `subCalendarMonth()` | ± 1 month |
| `addCalendarYears($n)` / `subCalendarYears($n)` | ± years with clamping |
| `addCalendarYear()` / `subCalendarYear()` | ± 1 year |
| `setCalendarDate($y, $m, $d, $calendar = null)` | set Y/M/D, keep time & TZ |

## Boundaries

```php
$date = Carbon::parse('2024-09-15 12:00:00')->withCalendar('jalali'); // 1403-06-25

$date->copy()->startOfCalendarMonth(); // 1403-06-01 00:00:00
$date->copy()->endOfCalendarMonth();   // 1403-06-31 23:59:59
$date->copy()->startOfCalendarYear();  // 1403-01-01 00:00:00
$date->copy()->endOfCalendarYear();    // 1403-12-30 23:59:59 (leap)
```

## Days and smaller units

Day / hour / minute arithmetic is calendar-independent - keep using Carbon:

```php
$date->addDays(3);
$date->subHours(2);
$date->diffInDays($other);
$date->diffForHumans();
```

## Queries by calendar month

```php
$from = Carbon::fromJalali(1403, 6, 1)->startOfDay();
$to = $from->copy()->endOfCalendarMonth();

Post::whereBetween('published_at', [$from, $to])->get();
```
