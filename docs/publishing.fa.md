# انتشار روی Read the Docs

این مخزن از قبل برای
[Read the Docs](https://readthedocs.org/) آماده است. بعد از اتصال ریپوی GitHub،
هر push به `master` (و هر تگ) مستندات را خودکار می‌سازد.

## راه‌اندازی یک‌باره

1. با GitHub در [readthedocs.org](https://readthedocs.org/) وارد شوید.
2. **Import a Project** → `hosni/boron` را انتخاب کنید.
3. اسلاگ پروژه را تأیید کنید (پیشنهادی: `boron` →
   `https://boron.readthedocs.io/`).
4. شاخهٔ پیش‌فرض را `master` نگه دارید.
5. اگر پیش‌نمایش PR می‌خواهید، **Build pull requests** را روشن کنید.

Read the Docs فایل [`.readthedocs.yaml`](https://github.com/hosni/boron/blob/master/.readthedocs.yaml)
را تشخیص می‌دهد و با MkDocs + Material می‌سازد.

## چه چیزهایی ساخته می‌شود

| فایل | نقش |
|---|---|
| `.readthedocs.yaml` | سیستم‌عامل، پایتون، مسیر تنظیمات MkDocs، وابستگی‌ها |
| `mkdocs.yml` | ساختار سایت، تم Material، ناوبری |
| `docs/requirements.txt` | نسخه‌های قفل‌شدهٔ MkDocs / Material / افزونه‌ها |
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

آدرس `http://127.0.0.1:8000/` را باز کنید. برای ساخت ایستا `mkdocs build`
(خروجی در `site/`، gitignored).

## نسخه‌ها و نام‌های مستعار

- **latest** — معمولاً `master` را دنبال می‌کند
- **stable** — در UI نسخه‌های RTD به یک تگ انتشار (مثلاً `v0.1.0`) اشاره دهید
- تگ‌های Git وقتی build برای تگ‌ها فعال باشد، مستندات نسخه‌دار می‌سازند

## دامنهٔ سفارشی (اختیاری)

در تنظیمات **Domains** پروژهٔ RTD مثلاً `docs.example.com` را اضافه کنید و
CNAME را به `readthedocs.io` بزنید.

## لینک Packagist / Composer

بعد از اولین build موفق، URL مستندات را در
`composer.json` → `support.docs` (از قبل روی
`https://boron.readthedocs.io` است) و در GitHub ریپو **About** → Website تنظیم کنید.
