# Publishing on Read the Docs

This repository is already wired for
[Read the Docs](https://readthedocs.org/). After you connect the GitHub repo,
every push to `master` (and every tag) builds the docs automatically.

## One-time setup

1. Sign in at [readthedocs.org](https://readthedocs.org/) with GitHub.
2. **Import a Project** → choose `hosni/boron`.
3. Confirm the project slug (suggested: `boron` →
   `https://boron.readthedocs.io/`).
4. Keep the default branch as `master`.
5. Ensure **Build pull requests** is enabled if you want PR previews.

Read the Docs will detect [`.readthedocs.yaml`](https://github.com/hosni/boron/blob/master/.readthedocs.yaml)
and build with MkDocs + Material.

## What gets built

| File | Purpose |
|---|---|
| `.readthedocs.yaml` | OS, Python, MkDocs config path, requirements |
| `mkdocs.yml` | Site structure, Material theme, nav |
| `docs/requirements.txt` | Pinned MkDocs / Material / extensions |
| `docs/**/*.md` | Content |
| `docs/javascript/readthedocs.js` | RTD search + version menu integration |
| `docs/overrides/main.html` | Material override for RTD addons |

## Local preview

```bash
python3 -m venv .venv-docs
source .venv-docs/bin/activate
pip install -r docs/requirements.txt
mkdocs serve
```

Open `http://127.0.0.1:8000/`. Build a static site with `mkdocs build`
(output in `site/`, gitignored).

## Versions & aliases

- **latest** - usually tracks `master`
- **stable** - point at a release tag (e.g. `v0.1.0`) in the RTD Versions UI
- Git tags become versioned docs automatically when builds are enabled for tags

## Custom domain (optional)

In the RTD project **Domains** settings, add e.g. `docs.example.com` and set
the CNAME to `readthedocs.io`.

## Packagist / composer link

After the first successful build, set the docs URL in
`composer.json` → `support.docs` (already set to
`https://boron.readthedocs.io`) and on the GitHub repo **About** → Website.
