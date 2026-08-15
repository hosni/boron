# محاسبات

`addMonth()` / `startOfMonth()` کربن روی ماه‌های **میلادی** کار می‌کنند. برای
تقویم فعال از متدهای آگاه از تقویم استفاده کنید.

## حساب ماه و سال

روزها به آخرین روز معتبر ماه مقصد **clamp** می‌شوند:

```php
use Boron\Carbon;

$date = Carbon::fromJalali(1403, 6, 31); // ۳۱ شهریور

$date->addCalendarMonths(1);  // 1403-07-30  (مهر ۳۰ روز دارد)
$date->addCalendarMonths(7);  // وارد ۱۴۰۴ می‌شود

Carbon::fromJalali(1403, 12, 30)->addCalendarYears(1);
// 1404-12-29  (اسفند ۳۰ در کبیسهٔ ۱۴۰۳ ← اسفند ۲۹ در ۱۴۰۴)
```

### متدها

| متد | اثر |
|---|---|
| `addCalendarMonths($n)` / `subCalendarMonths($n)` | ± ماه با clamp |
| `addCalendarMonth()` / `subCalendarMonth()` | ± ۱ ماه |
| `addCalendarYears($n)` / `subCalendarYears($n)` | ± سال با clamp |
| `addCalendarYear()` / `subCalendarYear()` | ± ۱ سال |
| `setCalendarDate($y, $m, $d, $calendar = null)` | تنظیم Y/M/D، نگه داشتن زمان و TZ |

## مرزها

```php
$date = Carbon::parse('2024-09-15 12:00:00')->withCalendar('jalali'); // 1403-06-25

$date->copy()->startOfCalendarMonth(); // 1403-06-01 00:00:00
$date->copy()->endOfCalendarMonth();   // 1403-06-31 23:59:59
$date->copy()->startOfCalendarYear();  // 1403-01-01 00:00:00
$date->copy()->endOfCalendarYear();    // 1403-12-30 23:59:59 (کبیسه)
```

## روز و واحدهای کوچک‌تر

حساب روز / ساعت / دقیقه مستقل از تقویم است — همان کربن را نگه دارید:

```php
$date->addDays(3);
$date->subHours(2);
$date->diffInDays($other);
$date->diffForHumans();
```

## پرس‌وجو بر اساس ماه تقویمی

```php
$from = Carbon::fromJalali(1403, 6, 1)->startOfDay();
$to = $from->copy()->endOfCalendarMonth();

Post::whereBetween('published_at', [$from, $to])->get();
```
