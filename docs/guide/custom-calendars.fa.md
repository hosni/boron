# تقویم‌های سفارشی

[`Boron\Calendars\CalendarInterface`](../api/calendar-drivers.md) را پیاده کنید
یا برای هر کلید تقویم ICU، wrapper ICU را دوباره استفاده کنید.

```php
use Boron\CalendarRegistry;
use Boron\Calendars\IcuCalendar;
use Boron\Carbon;

CalendarRegistry::register(
    'buddhist',
    fn () => new IcuCalendar('buddhist', 'buddhist'),
    aliases: ['thai'],
);

Carbon::now()->toCalendarDate('buddhist');
Carbon::fromCalendar('thai', 2567, 1, 1);
```

## قرارداد CalendarInterface

درایور شما باید این‌ها را فراهم کند:

- `getName(): string`
- `toJulianDayNumber(int $year, int $month, int $day): int`
- `fromJulianDayNumber(int $jdn): array` → `[year, month, day]`
- `isLeapYear`، `daysInMonth`، `daysInYear`، `monthsInYear`، `dayOfYear`
- `getMonthNames` / `getMonthName`
- `isValidDate`

برای تقویم‌های حسابی، از `Boron\Calendars\ArithmeticCalendar` ارث ببرید و
epoch، طول ماه‌ها و قواعد کبیسه را پیاده کنید.

## نکات ثبت

- کارخانه‌های تنبل (`fn () => new …`) ترجیح دهید تا درایورهای استفاده‌نشده هزینه ندهند.
- نام‌های مستعار از طریق `CalendarRegistry::get()` به همان نمونه resolve می‌شوند.
- دوبار صدا زدن `register()` با یک نام، درایور قبلی را جایگزین می‌کند.
