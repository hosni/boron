# Converting

```php
use Boron\Carbon;

$date = Carbon::parse('2024-03-20 15:30', 'Asia/Tehran');

$date->toJalali();                          // CalendarDate: 1403-01-01
$date->toHijri();                           // CalendarDate: 1445-09-10
$date->toGregorianDate();                   // CalendarDate: 2024-03-20
$date->toCalendarDate('hijri-umalqura');    // any registered calendar
$date->julianDayNumber();                   // 2460390
```

## CalendarDate

`toJalali()` / `toHijri()` / `toCalendarDate()` return a
[`CalendarDate`](../api/calendar-date.md) - an immutable year/month/day in one
calendar. Time and timezone stay on the Carbon instance; the value object is
date-only.

```php
$jalali = $date->toJalali();

$jalali->year;                              // 1403
$jalali->month;                             // 1
$jalali->day;                               // 1
$jalali->isLeapYear();                      // true
$jalali->getMonthName('fa');                // فروردین
$jalali->to('hijri');                       // convert again
$jalali->format('l j F Y', 'fa', true);     // چهارشنبه ۱ فروردین ۱۴۰۳
$jalali->toCarbon('Asia/Tehran');           // Boron\Carbon at midnight
$jalali->toCarbonImmutable();               // Boron\CarbonImmutable
```

!!! note
    `(string) $jalali` is always `YYYY-MM-DD` in that calendar (Latin digits).
