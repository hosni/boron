# نصب

```bash
composer require boron/carbon
```

## پیش‌نیازها

| پیش‌نیاز | محدودیت |
|---|---|
| PHP | `^8.1` |
| [nesbot/carbon](https://carbon.nesbot.com/) | `^3.0` |
| `ext-intl` | اختیاری — درایورهای ICU را فعال می‌کند (`*-intl`، `hijri-umalqura`) |

!!! tip "نسخه‌های لاراول"
    بورون به Carbon 3 نیاز دارد. یعنی **لاراول ۱۱، ۱۲ و ۱۳**. لاراول ۱۰ و پایین‌تر
    به Carbon 2 قفل شده‌اند و با این پکیج سازگار نیستند.

## اختیاری: intl

بدون `ext-intl`، درایورهای حسابی (`gregorian`، `jalali`، `hijri`،
`jalali-astronomical`) درست کار می‌کنند. افزونه را وقتی نصب کنید که لازم دارید:

- درایورهای ICU (`jalali-intl`، `hijri-intl`، `hijri-umalqura`، …)
- نام ماه‌های محلی برای localeهای دلخواه از طریق ICU

Composer این مورد را در `suggest` اعلام می‌کند:

```json
"suggest": {
    "ext-intl": "Enables the ICU-backed calendar drivers ..."
}
```

## تأیید نصب

```php
use Boron\Carbon;

echo Carbon::fromJalali(1403, 1, 1)->toDateString(); // 2024-03-20
```

در اپ لاراول، بعد از نصب:

```bash
php artisan about
```

باید بخش **Boron** را با نسخه، کلاس تاریخ، تقویم‌ها و وضعیت ICU ببینید.
