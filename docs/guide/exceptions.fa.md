# استثناها

همهٔ استثناهای بورون قرارداد `Boron\Exceptions\BoronException` را پیاده می‌کنند.

| استثنا | چه زمانی |
|---|---|
| `UnknownCalendarException` | `CalendarRegistry::get()` / resolve با نام ناشناخته |
| `InvalidCalendarDateException` | سال/ماه/روز برای آن تقویم نامعتبر است (مثلاً اسفند ۳۰ در سال غیرکبیسه) |
| `UnsupportedCalendarRangeException` | تاریخ قبل از سال ۱ تقویم مقصد |
| `InvalidFormatException` | `parseFromCalendar()` نمی‌تواند رشته را پارس کند |
| `IntlExtensionNotLoadedException` | درایور ICU خواسته شده ولی `ext-intl` نیست |

```php
use Boron\Carbon;
use Boron\Exceptions\InvalidFormatException;

try {
    $date = Carbon::parseFromCalendar('jalali', $request->input('date'));
} catch (InvalidFormatException $e) {
    // خطای اعتبارسنجی اضافه کنید
}
```
