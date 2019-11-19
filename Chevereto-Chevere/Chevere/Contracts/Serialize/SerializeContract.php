<?php

/*
 * This file is part of Chevere.
 *
 * (c) Rodolfo Berrios <rodolfo@chevereto.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Chevere\Contracts\Serialize;

use InvalidArgumentException;

interface SerializeContract
{
    /**
     * Creates a new instance.
     *
     * @throws InvalidArgumentException if $var can't be serialized
     */
    public function __construct($var);

    /**
     * Provides access to the serialized string.
     */
    public function toString();
}
