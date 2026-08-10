# Boron\\CarbonInterface

```php
namespace Boron;

interface CarbonInterface extends \Carbon\CarbonInterface
{
    // configuration, creation, conversion, modifiers, formatting, …
}
```

Extends Carbon's interface with the multi-calendar API. Type-hint this when you
need calendar methods; type-hint `\Carbon\CarbonInterface` when any Carbon is
enough.

## Configuration

```php
public static function setDefaultCalendar(string|CalendarInterface $calendar): void;
public static function getDefaultCalendar(): CalendarInterface;
public static function setDefaultCalendarLocale(string $locale): void;
public static function getDefaultCalendarLocale(): string;
```

## Creation

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

## Active calendar

```php
public function withCalendar(string|CalendarInterface|null $calendar): static;
public function getCalendar(): CalendarInterface;
public function getActiveCalendar(): CalendarInterface|string|null;
```

## Conversion

```php
public function julianDayNumber(): int;
public function toCalendarDate(string|CalendarInterface|null $calendar = null): CalendarDate;
public function toJalali(): CalendarDate;
public function toHijri(): CalendarDate;
public function toGregorianDate(): CalendarDate;
```

## Modifiers & boundaries

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

(Plus singular helpers: `addCalendarMonth()`, `subCalendarYear()`, …)

## Inspection & formatting

```php
public function isCalendarLeapYear(...): bool;
public function calendarDaysInMonth(...): int;
public function calendarFormat(string $format, ...): string;
public function toCalendarDateString(...): string;
public function toCalendarDateTimeString(...): string;
```

## Mutability (narrowed)

```php
public function toMutable(): Carbon;
public function toImmutable(): CarbonImmutable;
```
