# Calendar drivers

## CalendarInterface

```php
namespace Boron\Calendars;

interface CalendarInterface
{
    public function getName(): string;
    public function toJulianDayNumber(int $year, int $month, int $day): int;
    public function fromJulianDayNumber(int $julianDayNumber): array;
    public function isLeapYear(int $year): bool;
    public function daysInMonth(int $year, int $month): int;
    public function daysInYear(int $year): int;
    public function monthsInYear(int $year): int;
    public function dayOfYear(int $year, int $month, int $day): int;
    public function getMonthNames(string $locale = 'en'): array;
    public function getMonthName(int $month, string $locale = 'en'): string;
    public function isValidDate(int $year, int $month, int $day): bool;
}
```

## Built-in classes

| Class | Role |
|---|---|
| `ArithmeticCalendar` | Abstract base: epoch + month lengths + leap years → JDN |
| `GregorianCalendar` | Proleptic Gregorian |
| `JalaliCalendar` | 33-year-cycle Jalali (ICU-aligned) |
| `PersianAstronomicalCalendar` | date-object astronomical Jalali |
| `HijriCalendar` | Tabular Islamic |
| `IcuCalendar` | Wraps `IntlCalendar` for any ICU type |

### IcuCalendar

```php
use Boron\Calendars\IcuCalendar;

new IcuCalendar('persian', 'jalali-intl');
new IcuCalendar('islamic-umalqura', 'hijri-umalqura');
```

Requires `ext-intl`. Gregorian ICU instances are forced proleptic via
`setGregorianChange(-1.0e15)` so they match the arithmetic driver.
