# قالب‌بندی

## calendarFormat()

`calendarFormat()` توکن‌های PHP `date()` را می‌پذیرد. توکن‌های **تاریخ** در
تقویم انتخابی رندر می‌شوند؛ توکن‌های **زمان / timezone** به کربن سپرده می‌شوند:

```php
use Boron\Carbon;

$date = Carbon::parse('2024-03-20 14:05')->withCalendar('jalali');

$date->calendarFormat('Y/m/d');            // 1403/01/01
$date->calendarFormat('j F Y H:i');        // 1 Farvardin 1403 14:05
$date->calendarFormat('j F Y', 'hijri');   // 10 Ramadan 1445
$date->calendarFormat('l j F Y', locale: 'fa', localizeDigits: true);
// چهارشنبه ۱ فروردین ۱۴۰۳
```

### امضا

```php
public function calendarFormat(
    string $format,
    string|CalendarInterface|null $calendar = null,
    ?string $locale = null,
    bool $localizeDigits = false,
): string;
```

وقتی `$calendar` برابر `null` باشد، تقویم فعال استفاده می‌شود. وقتی `$locale`
برابر `null` باشد، locale پیش‌فرض تقویم استفاده می‌شود.

### توکن‌های آگاه از تقویم

`Y` `y` `m` `n` `d` `j` `t` `L` `z` `S` `F` `M` `l` `D` `N` `w`

نام روزهای هفته (`l`، `D`) از روز مطلق همان لحظه پیروی می‌کنند (مثل کربن) و
از طریق جدول weekday بورون محلی می‌شوند.

### ارقام

برای ارقام فارسی/عربی-هندی، `localizeDigits: true` بدهید (یا
`Boron\Support\Digits::localize()`). ارقام را دستی `str_replace` نکنید.

## میانبرها

```php
$date->toCalendarDateString();      // Y-m-d در تقویم فعال
$date->toCalendarDateTimeString();  // Y-m-d H:i:s (زمان از Carbon)
```
