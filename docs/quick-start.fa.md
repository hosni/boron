# شروع سریع

```php
use Boron\Carbon;

// تبدیل میلادی ← جلالی / هجری
$date = Carbon::parse('2024-03-20 15:30', 'Asia/Tehran');
$date->toJalali();   // CalendarDate 1403-01-01
$date->toHijri();    // CalendarDate 1445-09-10

// ساخت از تاریخ تقویمی
Carbon::fromJalali(1403, 1, 1);                 // 2024-03-20 00:00
Carbon::parseFromCalendar('jalali', '۱۴۰۳/۰۱/۰۱'); // ارقام فارسی OK

// قالب‌بندی برای نمایش
$date->calendarFormat('j F Y', 'jalali', 'fa', true);
// ۱ فروردین ۱۴۰۳

// حساب ماه آگاه از تقویم (clamp به آخرین روز معتبر)
Carbon::fromJalali(1403, 6, 31)->addCalendarMonths(1);
// 1403-07-30
```

## در لاراول

پکیج را نصب کنید — سرویس‌پرووایدر خودش ثبت می‌شود و
`Date::use(Boron\Carbon::class)` را صدا می‌زند:

```php
now()->toJalali();
$user->created_at->calendarFormat('Y/m/d', 'jalali');
```

برای casts، helperها و دستور `about` به
[یکپارچه‌سازی لاراول](laravel/integration.md) مراجعه کنید.
