# تغییرناپذیری

```php
use Boron\Carbon;
use Boron\CarbonImmutable;

$date = CarbonImmutable::now()->withCalendar('jalali');
$next = $date->addDay(); // نمونهٔ جدید؛ $date بدون تغییر

$next->getCalendar()->getName(); // jalali - تقویم از تغییرات جان سالم به‌در می‌برد
```

## تبدیل‌ها Carbon ساده نشت نمی‌دهند

`CarbonInterface` متدهای `toImmutable(): Carbon\CarbonImmutable` و
`toMutable(): Carbon\Carbon` را الزام می‌کند. بورون هر دو را محدود می‌کند:

```php
Carbon::now()->toImmutable();            // Boron\CarbonImmutable
CarbonImmutable::now()->toMutable();     // Boron\Carbon
```

تقویم فعال کپی می‌شود.

## سریال‌سازی

`serialize()` / `unserialize()` PHP تقویم فعال را نگه می‌دارند:

```php
$date = Carbon::parse('2024-03-20')->withCalendar('jalali');
$copy = unserialize(serialize($date));

$copy->getCalendar()->getName(); // jalali
```
