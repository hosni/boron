# انتشار روی Read the Docs

این مخزن برای [Read the Docs](https://readthedocs.org/) آماده است.
انگلیسی و فارسی **پروژه‌های جداگانهٔ RTD** هستند که به‌عنوان ترجمه به هم
وصل می‌شوند تا URLهای زنده پیشوند زبانی مورد انتظار RTD را داشته باشند:

| زبان | آدرس زنده |
|---|---|
| English | https://boron.readthedocs.io/en/latest/ |
| فارسی | https://boron.readthedocs.io/fa/latest/ |

MkDocs همچنان یک `mkdocs.yml` و فایل‌های پسوندی (`*.fa.md`) دارد. روی RTD،
`READTHEDOCS_LANGUAGE` باعث می‌شود `mkdocs-static-i18n` **فقط همان زبان** را
در ریشهٔ سایت بسازد (نه تو در تو زیر `/en/…/fa/…`).

## راه‌اندازی یک‌باره

### ۱. پروژهٔ انگلیسی (والد)

1. با GitHub در [readthedocs.org](https://readthedocs.org/) وارد شوید.
2. **Import a Project** → `hosni/boron` را انتخاب کنید.
3. اسلاگ را تأیید کنید (پیشنهادی: `boron` → `https://boron.readthedocs.io/`).
4. **Language** را روی English بگذارید.
5. شاخهٔ پیش‌فرض را `master` نگه دارید.
6. اگر پیش‌نمایش PR می‌خواهید، **Build pull requests** را روشن کنید.

### ۲. پروژهٔ فارسی (ترجمه)

Read the Docs مسیر `/fa/…` را فقط وقتی سرو می‌کند که پروژهٔ دومی وجود داشته
باشد و به‌عنوان ترجمهٔ والد لینک شده باشد.

1. دوباره از همان ریپوی `hosni/boron` یک **Import a Project** بزنید
   (اسلاگ پیشنهادی: `boron-fa`).
2. **Language** را روی **Persian** بگذارید (کد `fa`؛ در UI قدیمی ممکن است
   «Iranian» دیده شود).
3. همان [`.readthedocs.yaml`](https://github.com/hosni/boron/blob/master/.readthedocs.yaml)
   ریشهٔ مخزن را نگه دارید (پیش‌فرض).
4. پروژهٔ **والد** (`boron`) را باز کنید → **Translations** (یا
   Hosting → Translations) → `boron-fa` را اضافه کنید.

بعد از build هر دو پروژه، فارسی در
`https://boron.readthedocs.io/fa/latest/` است و سوییچ زبان Material به
`/en/` ↔ `/fa/` اشاره می‌کند (نسخه و مسیر صفحه حفظ می‌شود).

## چه چیزهایی ساخته می‌شود

| فایل | نقش |
|---|---|
| `.readthedocs.yaml` | سیستم‌عامل، پایتون، مسیر تنظیمات MkDocs، وابستگی‌ها |
| `mkdocs.yml` | ساختار سایت، تم Material، i18n، ناوبری |
| `docs/requirements.txt` | نسخه‌های قفل‌شدهٔ MkDocs / Material / `mkdocs-static-i18n` |
| `docs/**/*.md` | محتوا (انگلیسی و فارسی با پسوند `.fa.md`) |
| `docs/javascript/readthedocs.js` | یکپارچگی جستجو و منوی نسخهٔ RTD |
| `docs/overrides/main.html` | override تم Material برای addonsهای RTD |

## پیش‌نمایش محلی

```bash
python3 -m venv .venv-docs
source .venv-docs/bin/activate
pip install -r docs/requirements.txt
mkdocs serve
```

آدرس `http://127.0.0.1:8000/` (انگلیسی) و
`http://127.0.0.1:8000/fa/` (فارسی) را باز کنید. برای ساخت ایستا
`mkdocs build` (خروجی در `site/`، gitignored).

برای شبیه‌سازی build تک‌زبانهٔ RTD:

```bash
READTHEDOCS_LANGUAGE=en mkdocs build -d site-en
READTHEDOCS_LANGUAGE=fa mkdocs build -d site-fa
```

## نسخه‌ها و نام‌های مستعار

- **latest** — معمولاً `master` را دنبال می‌کند
- **stable** — در UI نسخه‌های RTD به یک تگ انتشار (مثلاً `v0.1.0`) اشاره دهید
- تگ‌های Git وقتی build برای تگ‌ها فعال باشد، مستندات نسخه‌دار می‌سازند

اگر URLهای هم‌تراز `/en/stable/` و `/fa/stable/` می‌خواهید، نسخه‌ها را روی
**هر دو** پروژهٔ انگلیسی و فارسی RTD تنظیم کنید.

## دامنهٔ سفارشی (اختیاری)

در تنظیمات **Domains** پروژهٔ **والد** RTD مثلاً `docs.example.com` را اضافه
کنید و CNAME را به `readthedocs.io` بزنید. ترجمه‌ها همان دامنه را زیر
`/fa/…` استفاده می‌کنند.

## لینک Packagist / Composer

بعد از اولین build موفق، URL مستندات را در
`composer.json` → `support.docs` (از قبل روی
`https://boron.readthedocs.io` است) و در GitHub ریپو **About** → Website تنظیم کنید.
