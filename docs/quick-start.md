# Quick start

```php
use Boron\Carbon;

// Convert Gregorian → Jalali / Hijri
$date = Carbon::parse('2024-03-20 15:30', 'Asia/Tehran');
$date->toJalali();   // CalendarDate 1403-01-01
$date->toHijri();    // CalendarDate 1445-09-10

// Create from a calendar date
Carbon::fromJalali(1403, 1, 1);                 // 2024-03-20 00:00
Carbon::parseFromCalendar('jalali', '۱۴۰۳/۰۱/۰۱'); // Persian digits OK

// Format for display
$date->calendarFormat('j F Y', 'jalali', 'fa', true);
// ۱ فروردین ۱۴۰۳

// Calendar-aware month math (clamps to last valid day)
Carbon::fromJalali(1403, 6, 31)->addCalendarMonths(1);
// 1403-07-30
```

## In Laravel

Install the package - the service provider auto-registers and calls
`Date::use(Boron\Carbon::class)`:

```php
now()->toJalali();
$user->created_at->calendarFormat('Y/m/d', 'jalali');
```

See [Laravel integration](laravel/integration.md) for casts, helpers, and the
`about` command.
