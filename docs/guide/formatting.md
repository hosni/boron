# Formatting

## calendarFormat()

`calendarFormat()` accepts PHP `date()` tokens. **Date** tokens are rendered in
the chosen calendar; **time / timezone** tokens are delegated to Carbon:

```php
use Boron\Carbon;

$date = Carbon::parse('2024-03-20 14:05')->withCalendar('jalali');

$date->calendarFormat('Y/m/d');            // 1403/01/01
$date->calendarFormat('j F Y H:i');        // 1 Farvardin 1403 14:05
$date->calendarFormat('j F Y', 'hijri');   // 10 Ramadan 1445
$date->calendarFormat('l j F Y', locale: 'fa', localizeDigits: true);
// چهارشنبه ۱ فروردین ۱۴۰۳
```

### Signature

```php
public function calendarFormat(
    string $format,
    string|CalendarInterface|null $calendar = null,
    ?string $locale = null,
    bool $localizeDigits = false,
): string;
```

When `$calendar` is `null`, the active calendar is used. When `$locale` is
`null`, the default calendar locale is used.

### Calendar-aware tokens

`Y` `y` `m` `n` `d` `j` `t` `L` `z` `S` `F` `M` `l` `D` `N` `w`

Weekday names (`l`, `D`) follow the absolute weekday of the instant (same as
Carbon), localized via Boron's weekday table.

### Digits

Pass `localizeDigits: true` (or use `Boron\Support\Digits::localize()`) for
Persian/Arabic-Indic numerals. Do not `str_replace` digits by hand.

## Shorthand helpers

```php
$date->toCalendarDateString();      // Y-m-d in the active calendar
$date->toCalendarDateTimeString();  // Y-m-d H:i:s (time from Carbon)
```
