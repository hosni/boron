# Boron\\CalendarDate

Immutable year/month/day triple in a specific calendar. Returned by
`toJalali()`, `toHijri()`, `toCalendarDate()`, etc.

```php
final class CalendarDate implements Stringable, JsonSerializable
{
    public readonly CalendarInterface $calendar;
    public readonly int $year;
    public readonly int $month;
    public readonly int $day;
}
```

## Construction

```php
use Boron\CalendarDate;
use Boron\CalendarRegistry;

new CalendarDate(CalendarRegistry::get('jalali'), 1403, 1, 1);
CalendarDate::fromJulianDayNumber($calendar, 2460390);
```

Invalid dates throw `InvalidCalendarDateException`.

## Conversion

```php
$jalali->to('hijri');                 // another CalendarDate
$jalali->toJulianDayNumber();         // int
$jalali->toCarbon('Asia/Tehran');     // Boron\Carbon at midnight
$jalali->toCarbonImmutable();         // Boron\CarbonImmutable
```

## Inspection

```php
$jalali->isLeapYear();
$jalali->daysInMonth();
$jalali->daysInYear();
$jalali->dayOfYear();
$jalali->dayOfWeek();                 // Monday-based index
$jalali->getMonthName('fa');
$jalali->equalTo($other);
$jalali->addDays(10);                 // new CalendarDate
```

## Formatting

```php
(string) $jalali;                     // 1403-01-01
$jalali->format('l j F Y', 'fa', true);
$jalali->toArray();
json_encode($jalali);
// {"calendar":"jalali","year":1403,"month":1,"day":1}
```
