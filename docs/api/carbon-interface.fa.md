# Boron\\CarbonInterface

```php
namespace Boron;

interface CarbonInterface extends \Carbon\CarbonInterface
{
    // پیکربندی، ساخت، تبدیل، modifierها، قالب‌بندی، …
}
```

اینترفیس کربن را با API چندتقویمی گسترش می‌دهد. وقتی به متدهای تقویم نیاز دارید
این را type-hint کنید؛ وقتی هر Carbon کافی است `\Carbon\CarbonInterface` را
type-hint کنید.

## پیکربندی

```php
public static function setDefaultCalendar(string|CalendarInterface $calendar): void;
public static function getDefaultCalendar(): CalendarInterface;
public static function setDefaultCalendarLocale(string $locale): void;
public static function getDefaultCalendarLocale(): string;
```

## ساخت

```php
public static function fromCalendar(...): static;
public static function fromJalali(...): static;
public static function fromHijri(...): static;
public static function parseFromCalendar(
    string|CalendarInterface $calendar,
    string $value,
    DateTimeZone|string|null $timezone = null,
): static;
```

## تقویم فعال

```php
public function withCalendar(string|CalendarInterface|null $calendar): static;
public function getCalendar(): CalendarInterface;
public function getActiveCalendar(): CalendarInterface|string|null;
```

## تبدیل

```php
public function julianDayNumber(): int;
public function toCalendarDate(string|CalendarInterface|null $calendar = null): CalendarDate;
public function toJalali(): CalendarDate;
public function toHijri(): CalendarDate;
public function toGregorianDate(): CalendarDate;
```

## Modifierها و مرزها

```php
public function setCalendarDate(int $year, int $month, int $day, ...): static;
public function addCalendarMonths(int $months = 1): static;
public function subCalendarMonths(int $months = 1): static;
public function addCalendarYears(int $years = 1): static;
public function subCalendarYears(int $years = 1): static;
public function startOfCalendarMonth(): static;
public function endOfCalendarMonth(): static;
public function startOfCalendarYear(): static;
public function endOfCalendarYear(): static;
```

(به‌علاوهٔ helperهای مفرد: `addCalendarMonth()`، `subCalendarYear()`، …)

## بازرسی و قالب‌بندی

```php
public function isCalendarLeapYear(...): bool;
public function calendarDaysInMonth(...): int;
public function calendarFormat(string $format, ...): string;
public function toCalendarDateString(...): string;
public function toCalendarDateTimeString(...): string;
```

## Mutability (محدودشده)

```php
public function toMutable(): Carbon;
public function toImmutable(): CarbonImmutable;
```
