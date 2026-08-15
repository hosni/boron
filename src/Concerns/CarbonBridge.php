<?php

declare(strict_types=1);

namespace Boron\Concerns;

use Carbon\Unit;
use Override;

/**
 * Glue used by the Boron classes that EXTEND Carbon
 * ({@see \Boron\Carbon} and {@see \Boron\CarbonImmutable}):
 * hooks the multi-calendar layer into the inherited Carbon members via
 * parent:: calls.
 */
trait CarbonBridge
{
    use MultiCalendar;

    #[Override]
    public function __serialize(): array
    {
        return $this->appendCalendarSerialization(parent::__serialize());
    }

    #[Override]
    public function __unserialize(array $data): void
    {
        parent::__unserialize($data);
        $this->restoreCalendarSerialization($data);
    }

    #[Override]
    public function get(Unit|string $name): mixed
    {
        return $this->resolveCalendarProperty($name) ?? parent::get($name);
    }
}
