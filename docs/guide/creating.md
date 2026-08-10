# Creating & parsing

## From calendar components

```php
use Boron\Carbon;

Carbon::fromJalali(1403, 1, 1);                              // 2024-03-20 00:00
Carbon::fromHijri(1445, 9, 10, 20, 30);                      // with time
Carbon::fromCalendar('hijri-umalqura', 1445, 9, 1);          // any calendar
Carbon::fromJalali(1403, 1, 1, 0, 0, 0, 'Asia/Tehran');      // timezone
```

Instances created this way keep that calendar **active**, so follow-up
`toCalendarDateString()` / `calendarYear` use it by default.

## Parsing strings

`parseFromCalendar()` accepts `Y/m/d`, `Y-m-d`, `Y.m.d`, optional `H:i[:s]`,
and Persian (۰-۹) or Arabic-Indic digits:

```php
Carbon::parseFromCalendar('jalali', '1403/01/01');
Carbon::parseFromCalendar('jalali', '1403-1-1 14:30');
Carbon::parseFromCalendar('jalali', '۱۴۰۳/۰۱/۰۱');
```

Bad input throws [`InvalidFormatException`](exceptions.md) — catch it in form
validation.

## From plain Carbon / DateTime

All of Carbon's factories still work:

```php
Carbon::now();
Carbon::parse('2024-03-20');
Carbon::createFromTimestamp(1_711_000_000);
Carbon::instance($dateTime);
```
