# Boron\\CarbonImmutable

```php
namespace Boron;

class CarbonImmutable extends \Carbon\CarbonImmutable implements CarbonInterface
{
    use Concerns\CarbonBridge;
}
```

همتای immutable [`Boron\Carbon`](carbon.md). همان API تقویم؛ هر modifier یک
نمونهٔ جدید برمی‌گرداند.

```php
use Boron\CarbonImmutable;

$date = CarbonImmutable::fromJalali(1403, 1, 1)->withCalendar('jalali');
$next = $date->addDay();          // نمونهٔ جدید
$date->toCalendarDateString();    // هنوز 1403-01-01
$next->toCalendarDateString();    // 1403-01-02

$date->toMutable();               // Boron\Carbon
```
