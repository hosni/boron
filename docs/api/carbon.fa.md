# Boron\\Carbon

```php
namespace Boron;

class Carbon extends \Carbon\Carbon implements CarbonInterface
{
    use Concerns\CarbonBridge;
}
```

زیرکلاس واقعی `Carbon\Carbon`. در لاراول (`Date::use`) و هرجا کد
`Carbon\Carbon` را type-hint می‌کند استفاده کنید.

## API تقویم

از [`CarbonInterface`](carbon-interface.md) از طریق concern به نام
`MultiCalendar` به ارث می‌رسد. نکات مهم:

```php
use Boron\Carbon;

Carbon::fromJalali(1403, 1, 1);
Carbon::fromHijri(1445, 9, 10);
Carbon::fromCalendar('jalali', 1403, 1, 1, timezone: 'Asia/Tehran');
Carbon::parseFromCalendar('jalali', '1403/01/01');

$date = Carbon::now()->withCalendar('jalali');
$date->toJalali();
$date->calendarFormat('j F Y', locale: 'fa', localizeDigits: true);
$date->addCalendarMonths(1);
$date->startOfCalendarMonth();
$date->toImmutable(); // Boron\CarbonImmutable
```

## پیکربندی

```php
Carbon::setDefaultCalendar('jalali');
Carbon::getDefaultCalendar();
Carbon::setDefaultCalendarLocale('fa');
Carbon::getDefaultCalendarLocale();
```

پیش‌فرض‌ها در سطح process هستند و با `CarbonImmutable` مشترک‌اند.

## API کربن

بدون تغییر. `parse`، `now`، `addDays`، `diffForHumans`، localization،
`setTestNow`، macros و … همه کار می‌کنند.
