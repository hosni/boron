# تقویم‌ها

بورون درایورهای حسابی (PHP خالص) و ICU (`ext-intl`) را همراه دارد. درایورهای
سفارشی را از طریق [`CalendarRegistry`](../api/calendar-registry.md) ثبت کنید.

## درایورهای داخلی

| نام | نام‌های مستعار | درایور | یادداشت |
|---|---|---|---|
| `gregorian` | `miladi` | حسابی | میلادی پروپتیک |
| `jalali` | `persian`، `shamsi` | حسابی | چرخهٔ ۳۳ ساله، هم‌راستا با ICU |
| `jalali-astronomical` | `persian-astronomical` | حسابی | جدول نجومی date-object |
| `hijri` | `islamic`، `arabic` | حسابی | هجری جدولی (مدنی) |
| `jalali-intl` | - | ICU | نیاز به `ext-intl` |
| `hijri-intl` | - | ICU | هجری مدنی ICU |
| `hijri-umalqura` | - | ICU | ام‌القرای عربستان |
| `hijri-astronomical` | - | ICU | ICU `islamic` |
| `gregorian-intl` | - | ICU | میلادی ICU (پروپتیک اجباری) |

```php
use Boron\CalendarRegistry;

CalendarRegistry::names();          // فهرست نام‌های ثبت‌شده
CalendarRegistry::has('jalali');    // true
CalendarRegistry::get('shamsi');    // همان درایور jalali
```

## انتخاب درایور

- **`jalali`** — جلالی پیش‌فرض اپ‌ها؛ در بازهٔ مدرن روزبه‌روز با ICU یکی است.
- **`jalali-astronomical`** — پورت وفادار جدول نجومی date-object؛ ممکن است حول
  چند سال کبیسهٔ تاریخی یک روز اختلاف داشته باشد (۱۳۰۸/۱۳۰۹، ۱۳۴۱/۱۳۴۲، ۱۴۷۳/۱۴۷۴).
- **`hijri`** — تقویم جدولی مدنی؛ ممکن است ±۱ روز با تقویم‌های رؤیت هلال فرق کند.
- **`hijri-umalqura`** — برای قواعد رسمی سعودی / مذهبی (نیاز به intl).

## بازهٔ پشتیبانی

از سال **۱** هر تقویم به بعد. تاریخ‌های میلادی قبل از ۶۲۲ م. در جلالی/هجری
قابل بیان نیستند و
[`UnsupportedCalendarRangeException`](exceptions.md) پرتاب می‌شود.

## نحوهٔ تبدیل

همهٔ تقویم‌ها با **شمارهٔ روز ژولیانی** (JDN مدنی) حرف می‌زنند. تبدیل A → B یعنی:

1. `A.toJulianDayNumber(y, m, d)`
2. `B.fromJulianDayNumber(jdn)`

همان پلی که ICU و date-object استفاده می‌کنند.
