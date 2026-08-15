# Boron\\CalendarRegistry

رجیستری مرکزی برای درایورهای تقویم، نام‌های مستعار و پیش‌فرض‌های سطح process.

```php
use Boron\CalendarRegistry;
use Boron\Calendars\IcuCalendar;

CalendarRegistry::get('jalali');
CalendarRegistry::get('shamsi');          // نام مستعار
CalendarRegistry::has('hijri-umalqura');
CalendarRegistry::names();
CalendarRegistry::resolve('jalali');      // string|CalendarInterface → درایور
CalendarRegistry::gregorian();            // helper تک‌نمونه‌ای GregorianCalendar

CalendarRegistry::register(
    'buddhist',
    fn () => new IcuCalendar('buddhist', 'buddhist'),
    ['thai'],
);

CalendarRegistry::setDefaultCalendar('jalali');
CalendarRegistry::getDefaultCalendar();
CalendarRegistry::setDefaultLocale('fa');
CalendarRegistry::getDefaultLocale();
```

!!! note
    از کد اپ، ترجیحاً `Boron\Carbon::setDefaultCalendar()` /
    `setDefaultCalendarLocale()` را صدا بزنید — به اینجا delegate می‌شوند و API
    عمومی روی کلاس‌های تاریخ می‌ماند.
