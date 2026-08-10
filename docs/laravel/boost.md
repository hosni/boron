# Laravel Boost (AI agents)

Boron ships [Laravel Boost](https://laravel.com/docs/boost) assets so AI agents
in Boost-enabled apps learn the multi-calendar API automatically.

## What is included

| Asset | Path | Role |
|---|---|---|
| Guidelines | `resources/boost/guidelines/core.blade.php` | Always-on context composed into `CLAUDE.md` / `AGENTS.md` |
| Skill | `resources/boost/skills/boron-development/SKILL.md` | On-demand Agent Skill with patterns and pitfalls |

## How discovery works

No code registration is required. When a project that depends on `hosni/boron`
runs:

```bash
php artisan boost:install
# or
php artisan boost:update
```

Boost scans installed packages for:

- `resources/boost/guidelines/core.blade.php`
- `resources/boost/skills/{skill-name}/SKILL.md`

and installs them based on user preference.

## Skill frontmatter

```yaml
---
name: boron-development
description: Work with dates in multiple calendars using Boron...
---
```

The `name` must match the directory name under `resources/boost/skills/`.
