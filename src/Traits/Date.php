<?php

declare(strict_types=1);

namespace Boron\Traits;

use Boron\Concerns\MultiCalendar;
use Carbon\Traits\Date as CarbonDate;
use Carbon\Unit;

/**
 * The Boron equivalent of Carbon's Date trait.
 *
 * It composes the whole of Carbon\Traits\Date (everything Carbon\Carbon
 * itself is made of) with the Boron multi-calendar layer, and is used by
 * the standalone classes {@see \Boron\Boron},
 * {@see \Boron\BoronMutable} and {@see \Boron\BoronImmutable},
 * which extend DateTime/DateTimeImmutable directly instead of Carbon.
 */
trait Date
{
    use CarbonDate, MultiCalendar {
        // Boron's toMutable()/toImmutable() replace Carbon's so that
        // conversions stay inside the Boron family.
        MultiCalendar::toMutable insteadof CarbonDate;
        MultiCalendar::toImmutable insteadof CarbonDate;

        // Keep Carbon's originals accessible for the overrides below.
        CarbonDate::get as private carbonGet;
        CarbonDate::__serialize as private carbonSerialize;
        CarbonDate::__unserialize as private carbonUnserialize;
    }

    public function __serialize(): array
    {
        return $this->appendCalendarSerialization($this->carbonSerialize());
    }

    public function __unserialize(array $data): void
    {
        $this->carbonUnserialize($data);
        $this->restoreCalendarSerialization($data);
    }

    /**
     * Carbon getters extended with the calendar-aware properties.
     */
    public function get(Unit|string $name): mixed
    {
        return $this->resolveCalendarProperty($name) ?? $this->carbonGet($name);
    }
}
