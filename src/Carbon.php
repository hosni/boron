<?php

declare(strict_types=1);

namespace Boron;

use Boron\Concerns\CarbonBridge;
use Carbon\Carbon as BaseCarbon;

/**
 * Drop-in replacement for Carbon\Carbon: a true Carbon subclass with the
 * Boron multi-calendar system on top.
 *
 * Because it extends Carbon, it can be handed to anything type-hinted
 * against Carbon\Carbon — including Laravel's Date::use(). Conversions
 * (toImmutable()/toMutable()) stay inside the Boron family instead of
 * leaking plain Carbon instances.
 *
 * If you don't need a literal Carbon subclass, the standalone
 * {@see Boron} class offers the same API on top of DateTime.
 */
class Carbon extends BaseCarbon implements BoronInterface
{
    use CarbonBridge;
}
