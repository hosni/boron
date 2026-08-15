# Publishing on Read the Docs

This repository is wired for [Read the Docs](https://readthedocs.org/).
English and Persian are **separate RTD projects** linked as translations, so
live URLs use the language prefix RTD expects:

| Language | Live URL |
|---|---|
| English | https://boron.readthedocs.io/en/latest/ |
| فارسی | https://boron.readthedocs.io/fa/latest/ |

MkDocs still uses one `mkdocs.yml` and suffix files (`*.fa.md`). On RTD,
`READTHEDOCS_LANGUAGE` makes `mkdocs-static-i18n` build **only that locale**
at the site root (not nested under `/en/…/fa/…`).

## One-time setup

### 1. English (parent) project

1. Sign in at [readthedocs.org](https://readthedocs.org/) with GitHub.
2. **Import a Project** → choose `hosni/boron`.
3. Confirm the slug (suggested: `boron` → `https://boron.readthedocs.io/`).
4. Set **Language** to English.
5. Keep the default branch as `master`.
6. Ensure **Build pull requests** is enabled if you want PR previews.

### 2. Persian (translation) project

Read the Docs serves `/fa/…` only when a second project exists and is linked
as a translation of the parent.

1. **Import a Project** again from the same `hosni/boron` repo (suggested
   slug: `boron-fa`).
2. Set **Language** to **Persian** (code `fa`; older UI may label it
   “Iranian”).
3. Point the project at the same [`.readthedocs.yaml`](https://github.com/hosni/boron/blob/master/.readthedocs.yaml)
   in the repo root (default).
4. Open the **parent** project (`boron`) → **Translations** (or
   Hosting → Translations) → add `boron-fa`.

After both projects build, Persian is at
`https://boron.readthedocs.io/fa/latest/` and the Material language switcher
targets `/en/` ↔ `/fa/` (version and page path preserved).

## What gets built

| File | Purpose |
|---|---|
| `.readthedocs.yaml` | OS, Python, MkDocs config path, requirements |
| `mkdocs.yml` | Site structure, Material theme, i18n, nav |
| `docs/requirements.txt` | Pinned MkDocs / Material / `mkdocs-static-i18n` |
| `docs/**/*.md` | Content (English + Persian via `*.fa.md` suffixes) |
| `docs/javascript/readthedocs.js` | RTD search + version menu integration |
| `docs/overrides/main.html` | Material override for RTD addons |

## Local preview

```bash
python3 -m venv .venv-docs
source .venv-docs/bin/activate
pip install -r docs/requirements.txt
mkdocs serve
```

Open `http://127.0.0.1:8000/` (English) and
`http://127.0.0.1:8000/fa/` (Persian). Build a static site with
`mkdocs build` (output in `site/`, gitignored).

To mimic RTD single-locale builds:

```bash
READTHEDOCS_LANGUAGE=en mkdocs build -d site-en
READTHEDOCS_LANGUAGE=fa mkdocs build -d site-fa
```

## Versions & aliases

- **latest** - usually tracks `master`
- **stable** - point at a release tag (e.g. `v0.1.0`) in the RTD Versions UI
- Git tags become versioned docs automatically when builds are enabled for tags

Configure versions on **both** the English and Persian RTD projects if you
want matching `/en/stable/` and `/fa/stable/` URLs.

## Custom domain (optional)

In the RTD **parent** project **Domains** settings, add e.g.
`docs.example.com` and set the CNAME to `readthedocs.io`. Translations
reuse the parent domain under `/fa/…`.

## Packagist / composer link

After the first successful build, set the docs URL in
`composer.json` → `support.docs` (already set to
`https://boron.readthedocs.io`) and on the GitHub repo **About** → Website.
