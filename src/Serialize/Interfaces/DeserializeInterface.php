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

namespace Chevere\Serialize\Interfaces;

use Chevere\Type\Interfaces\TypeInterface;
use LogicException;

/**
 * Describes the component in charge of handling `unserialize()`.
 */
interface DeserializeInterface
{
    /**
     * @throws LogicException
     */
    public function __construct(string $unserializable);

    /**
     * Provides access to the unserialize'd variable.
     */
    public function variable(): mixed;

    /**
     * Provides access to the TypeInterface instance for the unserialize variable.
     */
    public function type(): TypeInterface;
}
