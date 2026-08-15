# تقویم فعال

هر نمونه می‌تواند یک تقویم فعال داشته باشد که اعضای آگاه از تقویم از آن استفاده
می‌کنند. این کار getterها یا فرمت‌کننده‌های کربن را **عوض نمی‌کند**.

## به‌ازای هر نمونه

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
$date->calendarDate;             // نمونهٔ CalendarDate

$date->toCalendarDateString();      // 1403-01-01
$date->toCalendarDateTimeString();  // 1403-01-01 00:00:00

// getterهای کربن میلادی می‌مانند
$date->year;                 // 2024
$date->format('Y-m-d');      // 2024-03-20
```

`withCalendar(null)` override را پاک می‌کند تا نمونه دوباره پیش‌فرض سراسری را
دنبال کند.

## پیش‌فرض سراسری اپ

مشترک بین `Boron\Carbon` و `Boron\CarbonImmutable`:

```php
Carbon::setDefaultCalendar('jalali');
Carbon::setDefaultCalendarLocale('fa');

Carbon::now()->toCalendarDateString(); // مثلاً 1405-05-19
```

در لاراول یک‌بار در متد `boot()` یک سرویس‌پرووایدر تنظیم کنید.

## ویژگی‌های جادویی

| ویژگی | معنی |
|---|---|
| `calendarYear` | سال در تقویم فعال |
| `calendarMonth` | ماه (از ۱) |
| `calendarDay` | روز ماه |
| `calendarMonthName` | نام ماه محلی (locale پیش‌فرض) |
| `calendarDaysInMonth` | طول ماه جاری تقویم |
| `calendarDayOfYear` | روز سال از ۱ |
| `calendarName` | نام تقویم فعال |
| `calendarDate` | `CalendarDate` برای تقویم فعال |
| `julianDay` | شمارهٔ روز ژولیانی مدنی |
