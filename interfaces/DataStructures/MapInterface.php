<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevere.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Interfaces\DataStructures;

use Countable;
use Generator;

/**
 * Describes the component in charge of easing the work around `Ds\Map`.
 */
interface MapInterface extends Countable
{
    /**
     * Provides access to the map keys.
     */
    public function keys(): array;

    /**
     * Provides the generator.
     */
    public function getGenerator(): Generator;
}
