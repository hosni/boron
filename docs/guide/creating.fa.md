# ساخت و پارس

## از اجزای تقویم

```php
use Boron\Carbon;

Carbon::fromJalali(1403, 1, 1);                              // 2024-03-20 00:00
Carbon::fromHijri(1445, 9, 10, 20, 30);                      // با زمان
Carbon::fromCalendar('hijri-umalqura', 1445, 9, 1);          // هر تقویم
Carbon::fromJalali(1403, 1, 1, 0, 0, 0, 'Asia/Tehran');      // timezone
```

نمونه‌های ساخته‌شده این‌گونه، همان تقویم را **فعال** نگه می‌دارند؛ پس
`toCalendarDateString()` / `calendarYear` بعدی به‌طور پیش‌فرض از آن استفاده می‌کنند.

## پارس رشته‌ها

`parseFromCalendar()` قالب‌های `Y/m/d`، `Y-m-d`، `Y.m.d`، اختیاری `H:i[:s]`،
و ارقام فارسی (۰–۹) یا عربی-هندی را می‌پذیرد:

```php
Carbon::parseFromCalendar('jalali', '1403/01/01');
Carbon::parseFromCalendar('jalali', '1403-1-1 14:30');
Carbon::parseFromCalendar('jalali', '۱۴۰۳/۰۱/۰۱');
```

ورودی نامعتبر [`InvalidFormatException`](exceptions.md) می‌اندازد — در اعتبارسنجی
فرم آن را بگیرید.

## از Carbon / DateTime ساده

همهٔ factoryهای کربن همچنان کار می‌کنند:

```php
Carbon::now();
Carbon::parse('2024-03-20');
Carbon::createFromTimestamp(1_711_000_000);
Carbon::instance($dateTime);
```
