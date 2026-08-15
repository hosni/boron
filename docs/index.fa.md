# بورون

**جایگزین چندتقویمی و drop-in برای [Carbon](https://carbon.nesbot.com/).**

> بورون (B) عنصر **۵** جدول تناوبی است. کربن (C) عنصر **۶**.
> بورون درست کنار کربن نشسته — کمی سبک‌تر، و تقویم‌های بیشتری می‌شناسد.

بورون تقویم‌های جلالی (شمسی / هجری شمسی)، هجری قمری و میلادی را روی Carbon اضافه می‌کند.
کل API کربن را نگه می‌دارید — اختلاف زمانی، localization، `setTestNow()`، type-hint روی
`Carbon\Carbon` — و لایهٔ تقویمی می‌گیرید که آزادانه بین سامانه‌ها تبدیل می‌کند.

```php
use Boron\Carbon;

Carbon::parse('2024-03-20')->toJalali();          // 1403-01-01 (نوروز!)
Carbon::parse('2024-03-20')->toHijri();           // 1445-09-10 (۱۰ رمضان)
Carbon::fromJalali(1403, 5, 19)->toDateString();  // 2024-08-09

Carbon::now()->calendarFormat('l j F Y', 'jalali', 'fa', true);
// چهارشنبه ۱ فروردین ۱۴۰۳
```

## چرا بورون؟

- **بازنویسی کربن نیست** — `Boron\Carbon` از `Carbon\Carbon` ارث می‌برد؛ لاراول و
  هر type-hint کربن همچنان کار می‌کند.
- **دو خانوادهٔ درایور** — حسابی خالص PHP (الهام‌گرفته از
  [date-object](https://github.com/shahabyazdi/date-object)) و ICU از طریق `ext-intl`.
- **تقویم به‌عنوان لایهٔ نمایش** — لحظهٔ ذخیره‌شده میلادی می‌ماند؛ بقیهٔ تقویم‌ها
  نمایی برای ورودی و نمایش‌اند.
- **آمادهٔ لاراول** — سرویس‌پرووایدر خودکشف، Eloquent casts، `php artisan about`،
  و مهارت‌های AI در [Laravel Boost](laravel/boost.md).

## خانوادهٔ کلاس‌ها

| کلاس | ارث‌بری از | نقش |
|---|---|---|
| [`Boron\Carbon`](api/carbon.md) | `Carbon\Carbon` | جایگزین mutable |
| [`Boron\CarbonImmutable`](api/carbon-immutable.md) | `Carbon\CarbonImmutable` | جایگزین immutable |
| [`Boron\CarbonInterface`](api/carbon-interface.md) | `Carbon\CarbonInterface` | قرارداد آگاه از تقویم |

## زبان‌ها

این سایت به **انگلیسی** و **فارسی** در دسترس است — از سوییچ زبان در هدر استفاده کنید
(Material + `mkdocs-static-i18n`).

## گام‌های بعدی

1. [نصب](installation.md) پکیج
2. خواندن [شروع سریع](quick-start.md)
3. درک [مدل ذهنی](mental-model.md)
4. در صورت استفاده، اتصال به [لاراول](laravel/integration.md)
