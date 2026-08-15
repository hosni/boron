# بورون

[![Tests](https://github.com/hosni/boron/actions/workflows/tests.yml/badge.svg)](https://github.com/hosni/boron/actions/workflows/tests.yml)
[![Docs](https://readthedocs.org/projects/boron/badge/?version=latest)](https://boron.readthedocs.io/fa/latest/?badge=latest)
[![Latest Version](https://img.shields.io/packagist/v/boron/carbon.svg?label=packagist)](https://packagist.org/packages/boron/carbon)
[![PHP Version](https://img.shields.io/packagist/php-v/boron/carbon.svg)](https://packagist.org/packages/boron/carbon)
[![Total Downloads](https://img.shields.io/packagist/dt/boron/carbon.svg)](https://packagist.org/packages/boron/carbon)
[![License](https://img.shields.io/packagist/l/boron/carbon.svg)](https://github.com/hosni/boron/blob/master/LICENSE)

**جایگزین چندتقویمی و drop-in برای [Carbon](https://carbon.nesbot.com/).**

> بورون (B) عنصر **۵** جدول تناوبی است. کربن (C) عنصر **۶**.
> بورون درست کنار کربن نشسته — کمی سبک‌تر، و تقویم‌های بیشتری می‌شناسد. :)

**مستندات:** [boron.readthedocs.io](https://boron.readthedocs.io/fa/latest/) · [English README](README.md)

بورون یک **سامانهٔ چندتقویمی** روی Carbon اضافه می‌کند: جلالی (شمسی / هجری شمسی)،
هجری قمری و میلادی، با تبدیل آزادانه بین آن‌ها. کل قابلیت‌های کربن را دارید —
اختلاف زمانی، localization، `setTestNow()`، `CarbonInterface` و همه چیز.

موتور تقویم پورت PHP از طراحی Julian Day Number در
[shahabyazdi/date-object](https://github.com/shahabyazdi/date-object) است و هر
تقویم از طریق درایور دوم مبتنی بر افزونهٔ `intl` (ICU) هم در دسترس است.

```php
use Boron\Carbon;

Carbon::parse('2024-03-20')->toJalali();          // 1403-01-01 (نوروز!)
Carbon::parse('2024-03-20')->toHijri();           // 1445-09-10 (۱۰ رمضان)
Carbon::fromJalali(1403, 5, 19)->toDateString();  // 2024-08-09

Carbon::now()->calendarFormat('l j F Y', 'jalali', 'fa', true);
// چهارشنبه ۱ فروردین ۱۴۰۳
```

## خانوادهٔ کلاس‌ها

بورون Carbon را **بازنویسی نمی‌کند**. آن را گسترش می‌دهد:

| کلاس | ارث‌بری از | چه زمانی استفاده کنید |
|---|---|---|
| `Boron\Carbon` | `Carbon\Carbon` | تاریخ mutable — `Date::use()` لاراول، Eloquent casts، هر type-hint روی `Carbon\Carbon` |
| `Boron\CarbonImmutable` | `Carbon\CarbonImmutable` | تاریخ immutable |

هر دو `Boron\CarbonInterface` را پیاده می‌کنند که **`Carbon\CarbonInterface` را
گسترش می‌دهد** و API تقویم را اضافه می‌کند.

`toImmutable()` / `toMutable()` داخل خانوادهٔ بورون می‌مانند و هرگز نمونهٔ
Carbon ساده نشت نمی‌دهند؛ تقویم فعال همراه می‌آید.

## نصب

```bash
composer require boron/carbon
```

پیش‌نیاز: PHP 8.1+، `nesbot/carbon` ^3.0. افزونهٔ `intl` اختیاری است —
درایورهای ICU (`*-intl`، `hijri-umalqura`) را باز می‌کند.

## لاراول

**پشتیبانی‌شده: لاراول ۱۱، ۱۲ و ۱۳** — بورون به Carbon 3 نیاز دارد که لاراول از
`v11.0.0` پشتیبانی می‌کند (`nesbot/carbon: ^2.72.2|^3.0`؛ لاراول ۱۲+ فقط Carbon 3).
لاراول ۱۰ و پایین‌تر به Carbon 2 قفل‌اند و با بورون سازگار نیستند.

سرویس‌پرووایدر خودکشف می‌شود و `Date::use(\Boron\Carbon::class)` را صدا می‌زند؛
پس `now()`، `today()`، castهای تاریخ Eloquent و … همگی آگاه از تقویم می‌شوند:

```php
use Illuminate\Support\Facades\Date;

Date::now()->toJalali();                    // کار می‌کند، CalendarDate برمی‌گرداند
now()->toCalendarDateString('jalali');      // helperها Boron\Carbon برمی‌گردانند
User::first()->created_at->calendarFormat('Y/m/d', 'jalali');
```

castهای `datetime` مقدار `Boron\Carbon` و `immutable_datetime` مقدار
`Boron\CarbonImmutable` می‌دهند؛ سریال‌سازی JSON مدل‌ها قالب استاندارد ISO
لاراول را نگه می‌دارد. همهٔ این‌ها با سوئیت یکپارچه‌سازی Testbench
(`tests/Laravel`) پوشش داده شده‌اند.

بورون خودش را در `php artisan about` هم ثبت می‌کند (نسخه، درایورهای فعال، وضعیت ICU).

### Laravel Boost (عامل‌های هوش مصنوعی)

بورون دارایی‌های [Laravel Boost](https://laravel.com/docs/boost) را همراه دارد تا
عامل‌های AI در پروژه‌های مجهز به Boost خودکار بلد باشند چطور از آن استفاده کنند:

- **Guidelines** (`resources/boost/guidelines/core.blade.php`) — زمینهٔ همیشه روشن
  که با `php artisan boost:install` در `CLAUDE.md`/`AGENTS.md` بارگذاری می‌شود.
- **Skill** (`resources/boost/skills/boron-development/SKILL.md`) — مهارت عامل
  `boron-development` با الگوهای استفاده و دام‌ها.

## تقویم‌ها

| نام | نام‌های مستعار | درایور | یادداشت |
|---|---|---|---|
| `gregorian` | `miladi` | حسابی | میلادی پروپتیک |
| `jalali` | `persian`، `shamsi` | حسابی | چرخهٔ ۳۳ ساله، هم‌راستا با ICU |
| `jalali-astronomical` | `persian-astronomical` | حسابی | جدول نجومی date-object |
| `hijri` | `islamic`، `arabic` | حسابی | هجری جدولی (مدنی) |
| `jalali-intl` | - | ICU | نیاز به `ext-intl` |
| `hijri-intl` | - | ICU | هجری مدنی (ICU) |
| `hijri-umalqura` | - | ICU | ام‌القرای عربستان |
| `hijri-astronomical` | - | ICU | ICU `islamic` |
| `gregorian-intl` | - | ICU | میلادی ICU (پروپتیک اجباری) |

نکات:

- `jalali` حسابی در بازهٔ مدرن روزبه‌روز با ICU یکی است؛ درایور نجومی ممکن است
  حول چند سال کبیسهٔ تاریخی یک روز اختلاف داشته باشد (۱۳۰۸/۱۳۰۹، ۱۳۴۱/۱۳۴۲، ۱۴۷۳/۱۴۷۴).
- تاریخ‌های جدولی `hijri` تقریب *مدنی* هستند و ممکن است ±۱ روز با تقویم‌های
  مبتنی بر رؤیت فرق کنند؛ برای تقویم سعودی از `hijri-umalqura` استفاده کنید.
- بازهٔ پشتیبانی: از سال ۱ هر تقویم به بعد (تاریخ‌های میلادی قبل از ۶۲۲ م. در
  جلالی/هجری قابل بیان نیستند و استثنای بازه پرتاب می‌شود).

## استفاده

### تبدیل

```php
use Boron\Carbon;

$date = Carbon::parse('2024-03-20 15:30', 'Asia/Tehran');

$date->toJalali();               // CalendarDate: 1403-01-01
$date->toHijri();                // CalendarDate: 1445-09-10
$date->toCalendarDate('hijri-umalqura');  // هر تقویم ثبت‌شده
$date->julianDayNumber();        // 2460390

// CalendarDate یک value object کوچک و immutable است:
$jalali = $date->toJalali();
$jalali->year;                   // 1403
$jalali->month;                  // 1
$jalali->day;                    // 1
$jalali->isLeapYear();           // true
$jalali->getMonthName('fa');     // فروردین
$jalali->to('hijri');            // تبدیل دوباره
$jalali->format('l j F Y', 'fa', true); // چهارشنبه ۱ فروردین ۱۴۰۳
$jalali->toCarbon('Asia/Tehran'); // برگشت به Boron\Carbon در نیمه‌شب
```

### ساخت

```php
Carbon::fromJalali(1403, 1, 1);                     // 2024-03-20 00:00
Carbon::fromHijri(1445, 9, 10, 20, 30);             // با زمان
Carbon::fromCalendar('hijri-umalqura', 1445, 9, 1); // هر تقویم
Carbon::parseFromCalendar('jalali', '1403/01/01 14:30');
Carbon::parseFromCalendar('jalali', '۱۴۰۳/۰۱/۰۱');  // ارقام فارسی OK
```

### تقویم فعال

هر نمونه می‌تواند یک «تقویم فعال» داشته باشد که اعضای آگاه از تقویم به‌طور
پیش‌فرض از آن استفاده می‌کنند. رفتار کربن را **عوض نمی‌کند**:

```php
$date = Carbon::parse('2024-03-20')->withCalendar('jalali');

$date->calendarYear;         // 1403
$date->calendarMonth;        // 1
$date->calendarDay;          // 1
$date->calendarMonthName;    // Farvardin
$date->calendarDaysInMonth;  // 31
$date->calendarDayOfYear;    // 1
$date->toCalendarDateString();      // 1403-01-01
$date->toCalendarDateTimeString();  // 1403-01-01 00:00:00

$date->year;                 // 2024 - getterهای کربن دست‌نخورده
$date->format('Y-m-d');      // 2024-03-20 - format کربن دست‌نخورده
```

یا سراسری تنظیم کنید (مشترک بین `Carbon` و `CarbonImmutable`):

```php
Carbon::setDefaultCalendar('jalali');
Carbon::setDefaultCalendarLocale('fa');

Carbon::now()->toCalendarDateString();  // 1405-05-19
```

### قالب‌بندی

`calendarFormat()` توکن‌های PHP `date()` را می‌پذیرد. توکن‌های تاریخ
(`Y y m n d j t L z S F M l D N w`) در تقویم رندر می‌شوند؛ بقیه (زمان،
timezone، …) به کربن سپرده می‌شود:

```php
$date = Carbon::parse('2024-03-20 14:05')->withCalendar('jalali');

$date->calendarFormat('Y/m/d');            // 1403/01/01
$date->calendarFormat('j F Y H:i');        // 1 Farvardin 1403 14:05
$date->calendarFormat('j F Y', 'hijri');   // 10 Ramadan 1445
$date->calendarFormat('l j F Y', locale: 'fa', localizeDigits: true);
// چهارشنبه ۱ فروردین ۱۴۰۳
```

### محاسبات آگاه از تقویم

`addMonth()` و مشابه کربن همچنان روی تقویم میلادی کار می‌کنند. برای حساب ماه/سال
در تقویم فعال:

```php
$date = Carbon::fromJalali(1403, 6, 31);       // ۳۱ شهریور

$date->addCalendarMonths(1);                  // 1403-07-30 (clamp؛ مهر ۳۰ روز دارد)
Carbon::fromJalali(1403, 12, 30)->addCalendarYears(1);  // 1404-12-29 (clamp)

$date->startOfCalendarMonth();                // 1403-06-01 00:00:00
$date->endOfCalendarMonth();                  // 1403-06-31 23:59:59
$date->startOfCalendarYear();                 // 1403-01-01
$date->endOfCalendarYear();                   // 1403-12-30 (سال کبیسه!)

$date->setCalendarDate(1404, 1, 1);           // زمان و timezone را نگه می‌دارد
$date->isCalendarLeapYear();                  // بر اساس تقویم فعال
```

حساب روز/ساعت/… مستقل از تقویم است — همان `addDays()`، `addHours()`،
`diffInDays()` کربن را استفاده کنید.

### تغییرناپذیری

```php
use Boron\Carbon;
use Boron\CarbonImmutable;

$date = CarbonImmutable::now()->withCalendar('jalali');
$next = $date->addDay();     // نمونهٔ جدید، تقویم حفظ می‌شود

Carbon::now()->toImmutable();            // Boron\CarbonImmutable
CarbonImmutable::now()->toMutable();     // Boron\Carbon، هرگز Carbon ساده
```

### تقویم‌های سفارشی

`Boron\Calendars\CalendarInterface` را پیاده کنید (یا درایور ICU را برای هر کلید
تقویم ICU دوباره استفاده کنید) و ثبت کنید:

```php
use Boron\CalendarRegistry;
use Boron\Calendars\IcuCalendar;
use Boron\Carbon;

CalendarRegistry::register('buddhist', fn () => new IcuCalendar('buddhist'));

Carbon::now()->toCalendarDate('buddhist');
```

## تست

```bash
composer test
```

سوئیت شامل تست parity درایور جلالی حسابی در برابر تقویم فارسی ICU برای هر روز
بین ۱۹۰۰ تا ۲۱۰۰، تست‌های round-trip همهٔ درایورها در ۱۸۰۰–۲۲۰۰، و سوئیت
یکپارچه‌سازی لاراول روی
[Orchestra Testbench](https://github.com/orchestral/testbench)
(facade تاریخ، helperها، Eloquent casts، سریال‌سازی) است.

## سپاس

- [حسین حسنی](https://github.com/hosni) — نویسندهٔ بورون.
- [شهاب یزدی](https://github.com/shahabyazdi) — طراحی موتور تقویم و جدول کبیسهٔ
  نجومی فارسی از کتابخانهٔ
  [date-object](https://github.com/shahabyazdi/date-object) او آمده است.
- [Brian Nesbitt](https://github.com/briannesbitt)، [kylekatarnls](https://github.com/kylekatarnls) و [مشارکت‌کنندگان Carbon](https://github.com/CarbonPHP/carbon/graphs/contributors) برای پایهٔ کربنی که بورون روی آن ساخته شده.
- [ICU](https://icu.unicode.org/) — پیاده‌سازی مرجع پشت درایورهای `intl`.

## مجوز

MIT
