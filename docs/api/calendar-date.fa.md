# Boron\\CalendarDate

سه‌تایی immutable سال/ماه/روز در یک تقویم مشخص. خروجی
`toJalali()`، `toHijri()`، `toCalendarDate()` و مشابه.

```php
final class CalendarDate implements Stringable, JsonSerializable
{
    public readonly CalendarInterface $calendar;
    public readonly int $year;
    public readonly int $month;
    public readonly int $day;
}
```

## ساخت

```php
use Boron\CalendarDate;
use Boron\CalendarRegistry;

new CalendarDate(CalendarRegistry::get('jalali'), 1403, 1, 1);
CalendarDate::fromJulianDayNumber($calendar, 2460390);
```

تاریخ نامعتبر `InvalidCalendarDateException` می‌اندازد.

## تبدیل

```php
$jalali->to('hijri');                 // CalendarDate دیگر
$jalali->toJulianDayNumber();         // int
$jalali->toCarbon('Asia/Tehran');     // Boron\Carbon در نیمه‌شب
$jalali->toCarbonImmutable();         // Boron\CarbonImmutable
```

## بازرسی

```php
$jalali->isLeapYear();
$jalali->daysInMonth();
$jalali->daysInYear();
$jalali->dayOfYear();
$jalali->dayOfWeek();                 // اندیس مبتنی بر دوشنبه
$jalali->getMonthName('fa');
$jalali->equalTo($other);
$jalali->addDays(10);                 // CalendarDate جدید
```

## قالب‌بندی

```php
(string) $jalali;                     // 1403-01-01
$jalali->format('l j F Y', 'fa', true);
$jalali->toArray();
json_encode($jalali);
// {"calendar":"jalali","year":1403,"month":1,"day":1}
```
