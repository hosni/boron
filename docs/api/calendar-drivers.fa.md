# درایورهای تقویم

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

## کلاس‌های داخلی

| کلاس | نقش |
|---|---|
| `ArithmeticCalendar` | پایهٔ انتزاعی: epoch + طول ماه + کبیسه ← JDN |
| `GregorianCalendar` | میلادی پروپتیک |
| `JalaliCalendar` | جلالی چرخهٔ ۳۳ ساله (هم‌راستا با ICU) |
| `PersianAstronomicalCalendar` | جلالی نجومی date-object |
| `HijriCalendar` | هجری جدولی |
| `IcuCalendar` | wrapper روی `IntlCalendar` برای هر نوع ICU |

### IcuCalendar

```php
use Boron\Calendars\IcuCalendar;

new IcuCalendar('persian', 'jalali-intl');
new IcuCalendar('islamic-umalqura', 'hijri-umalqura');
```

نیاز به `ext-intl` دارد. نمونه‌های میلادی ICU با
`setGregorianChange(-1.0e15)` پروپتیک اجباری می‌شوند تا با درایور حسابی یکی شوند.
