# Boron\\Carbon

```php
namespace Boron;

class Carbon extends \Carbon\Carbon implements CarbonInterface
{
    use Concerns\CarbonBridge;
}
```

True subclass of `Carbon\Carbon`. Use this in Laravel (`Date::use`) and anywhere
code type-hints `Carbon\Carbon`.

## Calendar API

Inherited from [`CarbonInterface`](carbon-interface.md) via the
`MultiCalendar` concern. Highlights:

```php
use Boron\Carbon;

Carbon::fromJalali(1403, 1, 1);
Carbon::fromHijri(1445, 9, 10);
Carbon::fromCalendar('jalali', 1403, 1, 1, timezone: 'Asia/Tehran');
Carbon::parseFromCalendar('jalali', '1403/01/01');

$date = Carbon::now()->withCalendar('jalali');
$date->toJalali();
$date->calendarFormat('j F Y', locale: 'fa', localizeDigits: true);
$date->addCalendarMonths(1);
$date->startOfCalendarMonth();
$date->toImmutable(); // Boron\CarbonImmutable
```

## Configuration

```php
Carbon::setDefaultCalendar('jalali');
Carbon::getDefaultCalendar();
Carbon::setDefaultCalendarLocale('fa');
Carbon::getDefaultCalendarLocale();
```

Defaults are process-wide and shared with `CarbonImmutable`.

## Carbon API

Unchanged. `parse`, `now`, `addDays`, `diffForHumans`, localization,
`setTestNow`, macros, … all work.
