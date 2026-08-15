# Laravel Boost (عامل‌های هوش مصنوعی)

بورون دارایی‌های [Laravel Boost](https://laravel.com/docs/boost) را همراه دارد تا
عامل‌های AI در اپ‌های مجهز به Boost، API چندتقویمی را خودکار یاد بگیرند.

## چه چیزهایی همراه است

| دارایی | مسیر | نقش |
|---|---|---|
| Guidelines | `resources/boost/guidelines/core.blade.php` | زمینهٔ همیشه روشن که در `CLAUDE.md` / `AGENTS.md` ترکیب می‌شود |
| Skill | `resources/boost/skills/boron-development/SKILL.md` | Agent Skill درخواستی با الگوها و دام‌ها |

## نحوهٔ کشف

ثبت کدی لازم نیست. وقتی پروژه‌ای که به `boron/carbon` وابسته است اجرا می‌کند:

```bash
php artisan boost:install
# یا
php artisan boost:update
```

Boost پکیج‌های نصب‌شده را برای این‌ها اسکن می‌کند:

- `resources/boost/guidelines/core.blade.php`
- `resources/boost/skills/{skill-name}/SKILL.md`

و بر اساس ترجیح کاربر نصبشان می‌کند.

## Frontmatter مهارت

```yaml
---
name: boron-development
description: Work with dates in multiple calendars using Boron...
---
```

مقدار `name` باید با نام پوشه زیر `resources/boost/skills/` یکی باشد.
