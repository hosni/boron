# Exceptions

All Boron exceptions implement `Boron\Exceptions\BoronException`.

| Exception | When |
|---|---|
| `UnknownCalendarException` | `CalendarRegistry::get()` / resolve with an unknown name |
| `InvalidCalendarDateException` | Year/month/day is invalid for the calendar (e.g. Esfand 30 in a non-leap year) |
| `UnsupportedCalendarRangeException` | Date before year 1 of the target calendar |
| `InvalidFormatException` | `parseFromCalendar()` cannot parse the string |
| `IntlExtensionNotLoadedException` | An ICU driver is requested but `ext-intl` is missing |

```php
use Boron\Carbon;
use Boron\Exceptions\InvalidFormatException;

try {
    $date = Carbon::parseFromCalendar('jalali', $request->input('date'));
} catch (InvalidFormatException $e) {
    // add a validation error
}
```
