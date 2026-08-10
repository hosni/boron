<?php

declare(strict_types=1);

namespace Boron;

use Boron\Concerns\CarbonBridge;
use Carbon\CarbonImmutable as BaseCarbonImmutable;

/**
 * Drop-in replacement for Carbon\CarbonImmutable: a true CarbonImmutable
 * subclass with the Boron multi-calendar system on top.
 *
 * @see Carbon for the mutable drop-in variant.
 */
class CarbonImmutable extends BaseCarbonImmutable implements CarbonInterface
{
    use CarbonBridge;
}
