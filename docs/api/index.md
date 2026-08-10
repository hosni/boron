# API overview

Boron is a thin calendar layer on Carbon. Prefer these entry points:

| Type | Purpose |
|---|---|
| [`Boron\Carbon`](carbon.md) | Mutable date/time (extends `Carbon\Carbon`) |
| [`Boron\CarbonImmutable`](carbon-immutable.md) | Immutable variant |
| [`Boron\CarbonInterface`](carbon-interface.md) | Type-hint for calendar-aware Carbon |
| [`Boron\CalendarDate`](calendar-date.md) | Immutable Y/M/D in one calendar |
| [`Boron\CalendarRegistry`](calendar-registry.md) | Driver lookup, defaults, registration |
| [Calendar drivers](calendar-drivers.md) | `CalendarInterface` and built-ins |

Everything else is Carbon - see the
[Carbon documentation](https://carbon.nesbot.com/docs/).
