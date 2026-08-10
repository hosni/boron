# Custom calendars

Implement [`Boron\Calendars\CalendarInterface`](../api/calendar-drivers.md) or
reuse the ICU wrapper for any ICU calendar keyword.

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

## CalendarInterface contract

Your driver must provide:

- `getName(): string`
- `toJulianDayNumber(int $year, int $month, int $day): int`
- `fromJulianDayNumber(int $jdn): array` → `[year, month, day]`
- `isLeapYear`, `daysInMonth`, `daysInYear`, `monthsInYear`, `dayOfYear`
- `getMonthNames` / `getMonthName`
- `isValidDate`

For arithmetic calendars, extend `Boron\Calendars\ArithmeticCalendar` and
implement epoch, month lengths, and leap-year rules.

## Registration tips

- Prefer lazy factories (`fn () => new …`) so unused drivers cost nothing.
- Aliases resolve to the same instance via `CalendarRegistry::get()`.
- Calling `register()` twice with the same name replaces the previous driver.
