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
use TypeError;
use Chevere\Contracts\Type\TypeContract;

interface UnserializeContract
{
    /**
     * Creates a new instance.
     *
     * @throws InvalidArgumentException if $serialized can't be unserialized
     * @throws TypeError                if $serialized is not a RouteContract serialize
     */
    public function __construct(string $serialized);

    /**
     * Provides access to the unserialized variable.
     */
    public function var();

    /**
     * Provides access to the TypeContract instance.
     */
    public function type(): TypeContract;
}
