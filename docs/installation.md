# Installation

```bash
composer require hosni/boron
```

## Requirements

| Requirement | Constraint |
|---|---|
| PHP | `^8.1` |
| [nesbot/carbon](https://carbon.nesbot.com/) | `^3.0` |
| `ext-intl` | optional - unlocks ICU drivers (`*-intl`, `hijri-umalqura`) |

!!! tip "Laravel versions"
    Boron needs Carbon 3. That means **Laravel 11, 12, and 13**. Laravel 10 and
    below pin Carbon 2 and cannot use this package.

## Optional: intl

Without `ext-intl`, the arithmetic drivers (`gregorian`, `jalali`, `hijri`,
`jalali-astronomical`) work fine. Install the extension when you need:

- ICU drivers (`jalali-intl`, `hijri-intl`, `hijri-umalqura`, …)
- Localized month names for arbitrary locales via ICU

Composer advertises this under `suggest`:

```json
"suggest": {
    "ext-intl": "Enables the ICU-backed calendar drivers ..."
}
```

## Verify

```php
use Boron\Carbon;

echo Carbon::fromJalali(1403, 1, 1)->toDateString(); // 2024-03-20
```

In a Laravel app, after install:

```bash
php artisan about
```

You should see a **Boron** section with version, date class, calendars, and ICU status.
