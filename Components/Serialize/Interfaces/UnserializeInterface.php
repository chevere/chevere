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

namespace Chevere\Components\Serialize\Interfaces;

use Chevere\Components\Type\Interfaces\TypeInterface;

interface UnserializeInterface
{
    public function __construct(string $serialized);

    /**
     * Provides access to the unserialized variable.
     */
    public function var();

    /**
     * Provides access to the TypeInterface instance for the unserialized variable.
     */
    public function type(): TypeInterface;
}
