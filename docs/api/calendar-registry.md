# Boron\\CalendarRegistry

Central registry for calendar drivers, aliases, and process-wide defaults.

```php
use Boron\CalendarRegistry;
use Boron\Calendars\IcuCalendar;

CalendarRegistry::get('jalali');
CalendarRegistry::get('shamsi');          // alias
CalendarRegistry::has('hijri-umalqura');
CalendarRegistry::names();
CalendarRegistry::resolve('jalali');      // string|CalendarInterface → driver
CalendarRegistry::gregorian();            // GregorianCalendar singleton helper

CalendarRegistry::register(
    'buddhist',
    fn () => new IcuCalendar('buddhist', 'buddhist'),
    ['thai'],
);

CalendarRegistry::setDefaultCalendar('jalali');
CalendarRegistry::getDefaultCalendar();
CalendarRegistry::setDefaultLocale('fa');
CalendarRegistry::getDefaultLocale();
```

!!! note
    Prefer `Boron\Carbon::setDefaultCalendar()` / `setDefaultCalendarLocale()`
    from application code — they delegate here and keep the public API on the
    date classes.
