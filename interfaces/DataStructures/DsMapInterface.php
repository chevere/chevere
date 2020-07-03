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
use Ds\Map;

interface DsMapInterface extends Countable
{
    public function keys(): array;

    /**
     * @return Map A deep copied map
     */
    public function mapCopy(): Map;
}
