# تبدیل

```php
use Boron\Carbon;

$date = Carbon::parse('2024-03-20 15:30', 'Asia/Tehran');

$date->toJalali();                          // CalendarDate: 1403-01-01
$date->toHijri();                           // CalendarDate: 1445-09-10
$date->toGregorianDate();                   // CalendarDate: 2024-03-20
$date->toCalendarDate('hijri-umalqura');    // هر تقویم ثبت‌شده
$date->julianDayNumber();                   // 2460390
```

## CalendarDate

`toJalali()` / `toHijri()` / `toCalendarDate()` یک
[`CalendarDate`](../api/calendar-date.md) برمی‌گردانند — سه‌تایی immutable
سال/ماه/روز در یک تقویم. زمان و timezone روی نمونهٔ Carbon می‌مانند؛ این
value object فقط تاریخ است.

```php
$jalali = $date->toJalali();

$jalali->year;                              // 1403
$jalali->month;                             // 1
$jalali->day;                               // 1
$jalali->isLeapYear();                      // true
$jalali->getMonthName('fa');                // فروردین
$jalali->to('hijri');                       // تبدیل دوباره
$jalali->format('l j F Y', 'fa', true);     // چهارشنبه ۱ فروردین ۱۴۰۳
$jalali->toCarbon('Asia/Tehran');           // Boron\Carbon در نیمه‌شب
$jalali->toCarbonImmutable();               // Boron\CarbonImmutable
```

!!! note
    `(string) $jalali` همیشه `YYYY-MM-DD` در همان تقویم است (ارقام لاتین).
