# PHPStan

`Date::use(Boron\Carbon::class)` فقط **زمان اجرا** را عوض می‌کند. PHPStan همچنان
تگ‌های `@method` فیکید Laravel را می‌خواند که می‌گویند `Illuminate\Support\Carbon`؛
پس `Date::parse()->toJalali()` مثل متدی ناموجود به نظر می‌رسد.

بورون یک افزونهٔ PHPStan همراه دارد که:

1. کارخانه‌های `Date::*` و `now()` / `today()` را `Boron\Carbon` تایپ می‌کند.
2. API تقویم (`toJalali()`، `calendarFormat()`، `calendarYear`، ...) را روی
   `Illuminate\Support\Carbon` و `Carbon\CarbonImmutable` در دسترس می‌گذارد تا
   `$model->created_at->toJalali()` در Eloquent بدون جنگ با castهای Larastan
   type-check شود.
3. کارخانه‌های تقویم را روی facade تاریخ اضافه می‌کند: `Date::fromJalali()`،
   `Date::fromHijri()`، `Date::fromCalendar()`، `Date::parseFromCalendar()`،
   به‌علاوهٔ `Date::setDefaultCalendar()` / `getDefaultCalendar()` /
   `setDefaultCalendarLocale()` / `getDefaultCalendarLocale()`.

## نصب

اگر پروژه از [`phpstan/extension-installer`](https://github.com/phpstan/extension-installer)
استفاده کند، افزونه خودکار include می‌شود (`composer extra.phpstan.includes`).

وگرنه یک خط به `phpstan.neon` اضافه کنید:

```neon
includes:
    - vendor/boron/carbon/extension.neon
```

فایل stub جداگانه‌ای لازم نیست. Larastan اختیاری است؛ افزونه با PHPStan خالص
هم کار می‌کند.

## چه چیزی را عوض نمی‌کند

`Date::use()` متدهای `Illuminate\Support\Carbon::parse()` یا
`Carbon\Carbon::parse()` را جایگزین **نمی‌کند**. آن‌ها همچنان Carbon ساده
بدون متدهای تقویم برمی‌گردانند و اگر `toJalali()` صدا بزنید در زمان اجرا خطا
می‌دهند.

از یکی از این‌ها استفاده کنید:

```php
Date::parse('2024-03-20')->toJalali();
now()->toJalali();
$user->created_at->toJalali();
\Boron\Carbon::parse('2024-03-20')->toJalali();
```

PHPStan همچنان `Illuminate\Support\Carbon::parse()->toJalali()` را می‌پذیرد
چون attributeهای Eloquent از نوع `Support\Carbon` هستند. آن فراخوانی در زمان
اجرا خطاست — `Date::` / `now()` / Eloquent / `Boron\Carbon` را ترجیح دهید.

## انصراف

اگر پرووایدر بورون را غیرفعال می‌کنید
(`extra.laravel.dont-discover: ["boron/carbon"]`)، افزونهٔ PHPStan را هم
ignore کنید تا کارخانه‌های Date به‌اشتباه `Boron\Carbon` تایپ نشوند:

```json
{
    "extra": {
        "phpstan/extension-installer": {
            "ignore": ["boron/carbon"]
        }
    }
}
```

بدون installer، فقط `vendor/boron/carbon/extension.neon` را از `includes`
حذف کنید.
