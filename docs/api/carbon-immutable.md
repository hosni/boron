# Boron\\CarbonImmutable

```php
namespace Boron;

class CarbonImmutable extends \Carbon\CarbonImmutable implements CarbonInterface
{
    use Concerns\CarbonBridge;
}
```

Immutable counterpart of [`Boron\Carbon`](carbon.md). Same calendar API; every
modifier returns a new instance.

```php
use Boron\CarbonImmutable;

$date = CarbonImmutable::fromJalali(1403, 1, 1)->withCalendar('jalali');
$next = $date->addDay();          // new instance
$date->toCalendarDateString();    // still 1403-01-01
$next->toCalendarDateString();    // 1403-01-02

$date->toMutable();               // Boron\Carbon
```
