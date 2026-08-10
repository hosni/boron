# Immutability

```php
use Boron\Carbon;
use Boron\CarbonImmutable;

$date = CarbonImmutable::now()->withCalendar('jalali');
$next = $date->addDay(); // new instance; $date unchanged

$next->getCalendar()->getName(); // jalali — calendar survives modifications
```

## Conversions never leak plain Carbon

`CarbonInterface` requires `toImmutable(): Carbon\CarbonImmutable` and
`toMutable(): Carbon\Carbon`. Boron narrows both:

```php
Carbon::now()->toImmutable();            // Boron\CarbonImmutable
CarbonImmutable::now()->toMutable();     // Boron\Carbon
```

The active calendar is copied across.

## Serialization

PHP `serialize()` / `unserialize()` persist the active calendar:

```php
$date = Carbon::parse('2024-03-20')->withCalendar('jalali');
$copy = unserialize(serialize($date));

$copy->getCalendar()->getName(); // jalali
```
