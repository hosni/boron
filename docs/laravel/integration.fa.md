# یکپارچه‌سازی لاراول

## نسخه‌های پشتیبانی‌شده

| لاراول | Carbon | بورون |
|---|---|---|
| ۱۱ | Carbon 2 *یا* 3 | ✅ (Carbon 3 لازم است) |
| ۱۲ | Carbon 3 | ✅ |
| ۱۳ | Carbon 3 | ✅ |
| ≤ ۱۰ | Carbon 2 | ❌ |

## کار سرویس‌پرووایدر

`Boron\Laravel\BoronServiceProvider` خودکشف می‌شود و:

1. `Date::use(Boron\Carbon::class)` را صدا می‌زند تا `now()`، `today()`، facade
   `Date` و castهای Eloquent از نوع `datetime` مقدار `Boron\Carbon` برگردانند
   (`immutable_datetime` ← `Boron\CarbonImmutable`).
2. بخش **Boron** را در `php artisan about` ثبت می‌کند (نسخه، کلاس تاریخ،
   تقویم پیش‌فرض، locale، فهرست درایور، وضعیت ICU).

انصراف از discovery:

```json
"extra": {
    "laravel": {
        "dont-discover": ["boron/carbon"]
    }
}
```

## استفادهٔ معمول

```php
use Boron\Carbon;
use Illuminate\Support\Facades\Date;

Date::now()->toJalali();
now()->toCalendarDateString('jalali');
$user->created_at->calendarFormat('Y/m/d', 'jalali', 'fa', true);

// ورودی فرم ← مدل
$post->published_at = Carbon::parseFromCalendar('jalali', $request->input('published_at'));
```

## پیش‌فرض جلالی در کل اپ

```php
// AppServiceProvider::boot()
\Boron\Carbon::setDefaultCalendar('jalali');
\Boron\Carbon::setDefaultCalendarLocale('fa');
```

## Eloquent

کلاس cast ویژه‌ای لازم نیست. با پرووایدر ثبت‌شده:

- `protected $casts = ['published_at' => 'datetime'];` ← `Boron\Carbon`
- `'immutable_datetime'` ← `Boron\CarbonImmutable`

سریال‌سازی JSON همان قالب استاندارد ISO لاراول می‌ماند.

## PHPStan

لاراول `Date::parse()` را `Illuminate\Support\Carbon` تایپ می‌کند؛ پس PHPStan
`toJalali()` را نمی‌بیند مگر افزونهٔ بورون را بارگذاری کنید. ببینید
[PHPStan](phpstan.md) (`phpstan/extension-installer` یا یک خط `includes`).

## تست

پکیج یک سوئیت Orchestra Testbench زیر `tests/Laravel` دارد که facade تاریخ،
helperها، Eloquent casts، سریال‌سازی و دستور about را پوشش می‌دهد.
